<div class="sfilter-enquiry-form" id="sfilter-enquiry-form">

    <form action="" method="post">

        <div class="form-row">
            <label for="name"><?php _e('Name', 'sfilter'); ?></label>

            <input type="text" id="name" name="name" value="" required>
        </div>

        <div class="form-row">
            <label for="email"><?php _e('E-Mail', 'sfilter'); ?></label>

            <input type="email" id="email" name="email" value="" required>
        </div>

        <div class="form-row">
            <label for="message"><?php _e('Message', 'sfilter'); ?></label>

            <textarea name="message" id="message" required></textarea>
        </div>

        <div class="form-row">

            <?php wp_nonce_field('sf-enquiry-form'); ?>

            <input type="hidden" class="hidden" name="action" value="sf_enquiry" />
            <input type="submit" class="submit-enquiry" name="send_enquiry" value="<?php esc_attr_e('Send Enquiry', 'sfilter'); ?>" />
        </div>

    </form>
</div>
