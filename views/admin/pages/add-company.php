<?php

defined( 'ABSPATH' ) || exit; // Prevent direct access

wp_enqueue_media();
wp_enqueue_script( 'plotto' );

$company_name =
$company_content =
$company_logo = '';

if( isset( $_GET['cid'] ) )
{
    global $wpdb;
    $company_table = "{$wpdb->prefix}plot_companies";
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM `{$wpdb->prefix}plot_companies` WHERE `ID` = %d",
            $_GET['cid']
        )
    );

    if( ! empty( $results ) && is_array( $results ) )
    {
        $result = $results[0];
        $company_name = $result->name;
        $company_content = $result->description;
        $company_logo = $result->logo;
    }
}
?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="company-name"><?php _e( 'Name', 'plotto' ); ?></label>
                <input type="text" class="form-control" id="company-name" name="company_name" value="<?php echo $company_name; ?>">
            </div>
            <div class="form-group">
                <label for="company-content"><?php _e( 'Content', 'plotto' ); ?></label>
                <?php echo wp_editor( $company_content, 'company-content', [
                    'textarea_name' => 'company_content',
                    'drag_drop_upload' => true,
                    'textarea_rows' => 10,
                    'tinymce' => [
                        'menubar' => false,
                        'plugins' => 'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
                            'powerpaste','fullscreen','formatpainter','insertdatetime','media','table','help','wordcount',
                        'toolbar' => 'undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code table help'
                    ]
                ] ); ?>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="company-logo" class="w-100"><?php _e( 'Logo', 'plotto' ); ?></label>
                        <div class="company-logo-preview mb-3">
                            <?php if( ! empty( $company_logo ) ): ?>
                                <img src="<?php echo wp_get_attachment_url( $company_logo ); ?>" alt="" width="150">
                            <?php else: ?>
                            <img src="<?php echo wc_placeholder_img_src( 'thumbnail' ); ?>" alt="" width="150">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-primary upload-company-logo">
                            <?php echo ! empty( $company_logo ) ? __( 'Change logo', 'plotto' ) : __( 'Upload logo', 'plotto' ); ?>
                        </button>
                        <input type="hidden" name="company_logo_url" id="company-logo-url" value="<?php echo ! empty( $company_logo ) ? wp_get_attachment_url( $company_logo ) : ''; ?>">
                        <input type="hidden" name="company_logo_id" id="company-logo-id" value="<?php echo $company_logo; ?>">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary save-company" data-cid="<?php echo isset( $_GET['cid'] ) ? $_GET['cid'] : '0'; ?>"><?php _e( 'Save', 'plotto' ); ?></button>
        </div>
    </div>
</section>