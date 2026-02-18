<?php

namespace SFilter\Checkout;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDF
{
    /**
     * @var Dompdf
     */
    protected $pdf;

    /**
     * @var int
     */
    protected $order_id;

    /**
     * @var array
     */
    protected $customer_data;

    /**
     * Constructor
     *
     * @param int $order_id
     * @param array $customer_data
     */
    public function __construct($order_id, $customer_data)
    {
        $this->order_id = $order_id;
        $this->customer_data = $customer_data;
        $this->init_pdf();
    }

    /**
     * Initialize Dompdf instance
     */
    protected function init_pdf()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $this->pdf = new Dompdf($options);
        $this->pdf->setPaper('A4', 'portrait');
    }

    /**
     * Generate the quotation PDF
     *
     * @return string|false PDF file path or false on failure
     */
    public function generate()
    {
        $order = wc_get_order($this->order_id);

        if (!$order) {
            return false;
        }

        // Generate HTML content
        $html = $this->get_pdf_content($order);

        // Load HTML into Dompdf
        $this->pdf->loadHtml($html);

        // Render the PDF
        $this->pdf->render();

        // Create upload directory if it doesn't exist
        $upload_dir = $this->get_upload_dir();
        if (!file_exists($upload_dir)) {
            wp_mkdir_p($upload_dir);

            // Add .htaccess for security
            $htaccess = $upload_dir . '/.htaccess';
            if (!file_exists($htaccess)) {
                file_put_contents($htaccess, "Options -Indexes\n");
            }
        }

        // Generate file path
        $filename = sprintf('%d-quotation.pdf', $this->order_id);
        $filepath = $upload_dir . '/' . $filename;

        // Output PDF to file
        $output = $this->pdf->output();
        file_put_contents($filepath, $output);

        if (file_exists($filepath)) {
            // Save meta data to order (HPOS compatible)
            $file_url = $this->get_upload_url() . '/' . $filename;
            $order->update_meta_data('_sf_quotation_pdf_path', $filepath);
            $order->update_meta_data('_sf_quotation_pdf_url', $file_url);
            $order->save();

            return $filepath;
        }

        return false;
    }

    /**
     * Get the PDF content HTML
     *
     * @param \WC_Order $order
     * @return string
     */
    protected function get_pdf_content($order)
    {
        ob_start();
        include __DIR__ . '/views/pdf-template.php';
        return ob_get_clean();
    }

    /**
     * Get upload directory path
     *
     * @return string
     */
    protected function get_upload_dir()
    {
        $upload = wp_upload_dir();
        return $upload['basedir'] . '/sfilter-quotations';
    }

    /**
     * Get upload directory URL
     *
     * @return string
     */
    protected function get_upload_url()
    {
        $upload = wp_upload_dir();
        return $upload['baseurl'] . '/sfilter-quotations';
    }

    /**
     * Get the generated PDF URL (HPOS compatible)
     *
     * @return string|null
     */
    public function get_pdf_url()
    {
        $order = wc_get_order($this->order_id);
        if (!$order) {
            return null;
        }
        return $order->get_meta('_sf_quotation_pdf_url') ?: null;
    }

    /**
     * Delete the PDF file (HPOS compatible)
     *
     * @param int $order_id
     * @return bool
     */
    public static function delete($order_id)
    {
        $order = wc_get_order($order_id);
        if (!$order) {
            return false;
        }

        $filepath = $order->get_meta('_sf_quotation_pdf_path');

        if ($filepath && file_exists($filepath)) {
            unlink($filepath);
            $order->delete_meta_data('_sf_quotation_pdf_path');
            $order->delete_meta_data('_sf_quotation_pdf_url');
            $order->save();
            return true;
        }

        return false;
    }
}
