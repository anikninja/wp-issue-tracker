<?php
/**
 * Issue Tracker issue-tracker-modal
 *
 * @package WebsiteIssueTracker
 * @version 1.0.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

$fields = Website_Issue_Tracker_Form_Handler::get_instance()->get_feedback_fields();
?>
    <div class="issue-tracker-modal" id="issue-tracker-feedback" tabindex="-1" role="dialog" style="display: none;">
        <div class="issue-tracker-modal-dialog" role="document">
            <div class="issue-tracker-modal-content">
                <div class="issue-tracker-modal-header">
                    <h4 class="issue-tracker-modal-title"><?php esc_html_e( 'Feedback', 'website-issue-tracker' ); ?></h4>
                    <a href="#" class="close" role="button" aria-label="<?php esc_html_e( 'Close', 'website-issue-tracker' ); ?>">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="issue-tracker-modal-body">
                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                        <?php wp_nonce_field( 'website-issue-tracker-feedback', '_issue_tracker_feedback_nonce' ); ?>
                        <input type="hidden" name="action" value="wp_issue_tracker_feedback">
                        <?php
                            foreach ( $fields as $key => $field ) {
                                wp_issue_tracker_form_field( $key, $field );
                            }
                        ?>
                        <div class="submit-feedback">
                            <input type="submit" name="submit" value="<?php esc_attr_e( 'Submit Feedback', 'website-issue-tracker' ); ?>">
                        </div>
                    </form>
                </div>
                <div class="issue-tracker-modal-footer"></div>
            </div>
        </div>
    </div>
<?php
// End for file issue-tracker-issue-tracker-modal.php.
