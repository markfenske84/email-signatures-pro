<?php
/**
 * Template for single Signature post.
 *
 * This template is loaded automatically by Email Signatures Pro plugin.
 * It prints a minimal markup for an email signature preview.
 * Access is already restricted to logged-in users in plugin core.
 *
 * @var WP_Post $post
 */

global $post;

// Retrieve meta values.
$job_title    = get_post_meta( $post->ID, '_esp_job_title', true );
$phone_number = get_post_meta( $post->ID, '_esp_phone_number', true );
$meeting_url  = get_post_meta( $post->ID, '_esp_meeting_url', true );
$avatar_url   = get_the_post_thumbnail_url( $post, 'medium' );

$options      = get_option( 'esp_settings', array() );
$company_logo = $options['company_logo'] ?? '';
$cta_button   = $options['cta_button'] ?? '';
$primary      = $options['primary_color'] ?? '#000000';
$secondary    = $options['secondary_color'] ?? '#777777';
$neutral      = $options['neutral_color'] ?? '#cccccc';
$tertiary     = $options['tertiary_color'] ?? '#aaaaaa';
$fonts_url    = $options['fonts_url'] ?? '';
$heading_css  = $options['heading_font_css'] ?? 'Arial, sans-serif';
$body_css     = $options['body_font_css'] ?? 'Arial, sans-serif';

// Sanitize phone number for tel link (digits only) and prepare display version with dot separators.
$phone_digits = preg_replace( '/\D+/', '', $phone_number ); // keep digits only

// Default to the raw digits if we cannot determine a sensible grouping.
$phone_display = $phone_digits;

// Format common phone lengths with dot separators (e.g., 123.456.7890).
if ( 10 === strlen( $phone_digits ) ) {
    $phone_display = substr( $phone_digits, 0, 3 ) . '.' . substr( $phone_digits, 3, 3 ) . '.' . substr( $phone_digits, 6 );
} elseif ( 11 === strlen( $phone_digits ) && '1' === $phone_digits[0] ) { // North-American 1+ number
    $phone_display = substr( $phone_digits, 1, 3 ) . '.' . substr( $phone_digits, 4, 3 ) . '.' . substr( $phone_digits, 7 );
} elseif ( 7 === strlen( $phone_digits ) ) {
    $phone_display = substr( $phone_digits, 0, 3 ) . '.' . substr( $phone_digits, 3 );
}

// Site URL from plugin settings, fall back to WordPress site URL.
$site_url_raw = ! empty( $options['website_url'] ) ? $options['website_url'] : site_url();
$site_domain  = preg_replace( '/https?:\/\/(www\.)?/', '', $site_url_raw );

// Individual text images.
$signature_name_img_id   = get_post_meta( $post->ID, '_esp_signature_image_name', true );
$signature_title_img_id  = get_post_meta( $post->ID, '_esp_signature_image_title', true );
$signature_name_img_url  = $signature_name_img_id ? wp_get_attachment_url( $signature_name_img_id ) : '';
$signature_title_img_url = $signature_title_img_id ? wp_get_attachment_url( $signature_title_img_id ) : '';

// Phone & site images.
$signature_phone_img_id = get_post_meta( $post->ID, '_esp_signature_image_phone', true );
$signature_phone_img_url = $signature_phone_img_id ? wp_get_attachment_url( $signature_phone_img_id ) : '';

$signature_site_img_id  = get_post_meta( $post->ID, '_esp_signature_image_site', true );
$signature_site_img_url = $signature_site_img_id ? wp_get_attachment_url( $signature_site_img_id ) : '';

// Bottom-only phone (digits, no "M") image.
$signature_phone_only_img_id = get_post_meta( $post->ID, '_esp_signature_image_phone_only', true );
$signature_phone_only_img_url = $signature_phone_only_img_id ? wp_get_attachment_url( $signature_phone_only_img_id ) : '';

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( get_the_title() ); ?> – Signature</title>
    <?php if ( $fonts_url ) : ?>
        <link rel="stylesheet" href="<?php echo esc_url( $fonts_url ); ?>" />
    <?php endif; ?>
    <style>
        body{font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $secondary ); ?>;padding:40px;background:#f8f9fa;}
        .signature-card{background:#fff;max-width:600px;margin:0 auto;padding:24px;}
        .signature-name{font-family:<?php echo esc_html( $heading_css ); ?>;font-size:32px;font-weight:700;text-transform:uppercase;color:<?php echo esc_html( $primary ); ?>;margin:0;line-height:1;}
        .signature-title{margin:4px 0 16px;font-size:18px;text-transform:uppercase;color:<?php echo esc_html( $neutral ); ?>;line-height:1;}
        .signature-cta-line{text-align:left;margin:0;}
        .signature-avatar img{width:140px;height:140px;border-radius:50%;object-fit:cover;}
        .signature-cta img{max-height:48px;display:block;}
        .social-icons img{width:24px;height:24px;margin-right:6px;}
        .signature-bottom td{font-size:16px;}
        .social-icons a{display:inline-block;}
    </style>
</head>
<body>
    <?php if ( current_user_can( 'edit_post', $post->ID ) ) : ?>
        <button id="esp-regenerate-btn" style="margin-bottom:20px;margin-right:10px;padding:10px 16px;background:<?php echo esc_html( $neutral ); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;">Regenerate Signature</button>
    <?php endif; ?>

    <?php if ( current_user_can( 'read' ) ) : ?>
        <button id="esp-copy-btn" style="display:none;margin-bottom:20px;padding:10px 16px;background:<?php echo esc_html( $primary ); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;">Copy Signature</button>
    <?php endif; ?>
    <div class="signature-card">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr style="display:block;margin-bottom:10px;">
                <!-- Avatar -->
                <td width="140" class="signature-avatar" valign="top" align="left" style="padding-right:20px;">
                    <?php if ( $avatar_url ) : ?>
                        <img src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar" style="width:140px;height:140px;border-radius:50%;object-fit:cover;" />
                    <?php elseif( ! empty( $options['default_avatar'] ) ) : ?>
                        <img src="<?php echo esc_url( $options['default_avatar'] ); ?>" alt="Avatar" style="width:140px;height:140px;border-radius:50%;object-fit:cover;" />
                    <?php endif; ?>
                </td>

                <!-- Main details -->
                <td valign="top" align="left">
                    <?php if ( $signature_name_img_url ) : ?>
                        <img src="<?php echo esc_url( $signature_name_img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" style="display:block;max-width:100%;height:40px;margin:0;" />
                    <?php else : ?>
                        <p class="signature-name esp-render" data-field="name" style="margin:0;display:inline-block;white-space:nowrap;line-height:1;"><?php echo esc_html( get_the_title() ); ?></p>
                    <?php endif; ?>

                    <?php if ( $signature_title_img_url ) : ?>
                        <img src="<?php echo esc_url( $signature_title_img_url ); ?>" alt="<?php echo esc_attr( $job_title ); ?>" style="display:block;max-width:100%;height:20px;margin:4px 0 16px;" />
                    <?php else : ?>
                        <p class="signature-title esp-render" data-field="title" style="margin:4px 0 16px;font-size:18px;display:inline-block;white-space:nowrap;line-height:1;"><?php echo esc_html( $job_title ); ?></p>
                    <?php endif; ?>

                    <!-- CTA + Phone -->
                    <table cellpadding="0" cellspacing="0" class="signature-cta-line">
                        <tr>
                            <?php if ( $cta_button ) : ?>
                                <td class="signature-cta" style="padding-right:18px;">
                                    <a href="<?php echo esc_url( $meeting_url ); ?>" target="_blank"><img src="<?php echo esc_url( $cta_button ); ?>" alt="CTA" style="display:block;max-height:40px;" /></a>
                                </td>
                            <?php endif; ?>
                            <td style="font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $secondary ); ?>;font-size:16px;white-space:nowrap;">
                                <a href="tel:<?php echo esc_attr( $phone_digits ); ?>" style="color:<?php echo esc_html( $secondary ); ?>;text-decoration:none;font-weight:600;">
                                    <?php if ( $signature_phone_img_url ) : ?>
                                        <img src="<?php echo esc_url( $signature_phone_img_url ); ?>" alt="<?php echo esc_attr( 'M ' . $phone_display ); ?>" style="display:inline-block;max-height:24px;vertical-align:middle;" />
                                    <?php else : ?>
                                        <span class="esp-render" data-field="phone"><small style="font-weight:400;">M</small> <?php echo esc_html( $phone_display ); ?></span>
                                    <?php endif; ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Divider -->
            <tr>
                <td colspan="2" height="1" style="opacity:0.25;line-height:1px;font-size:1px;background:<?php echo esc_html( $neutral ); ?>;margin:0;padding:0;"></td>
            </tr>

            <!-- Bottom row -->
            <tr class="signature-bottom">
                <td colspan="2">
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 6px 0;">
                        <tr>
                            <!-- Company Logo -->
                            <td valign="middle" style="padding-right:20px;white-space:nowrap;">
                                <?php if ( $company_logo ) : ?>
                                    <a href="<?php echo esc_url( $site_url_raw ); ?>" target="_blank" style="display:inline-block;">
                                        <img src="<?php echo esc_url( $company_logo ); ?>" alt="Company Logo" style="max-height:35px;margin-top:2px;" />
                                    </a>
                                <?php endif; ?>
                            </td>

                            <!-- Phone bottom -->
                            <td valign="middle" style="font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $neutral ); ?>;font-size:16px;white-space:nowrap;">
                                <a href="tel:<?php echo esc_attr( $phone_digits ); ?>" style="color:<?php echo esc_html( $neutral ); ?>;text-decoration:none;font-weight:600;">
                                    <?php if ( $signature_phone_only_img_url ) : ?>
                                        <img src="<?php echo esc_url( $signature_phone_only_img_url ); ?>" alt="<?php echo esc_attr( $phone_display ); ?>" style="display:inline-block;max-height:24px;vertical-align:middle;margin-top:-6px;" />
                                    <?php else : ?>
                                        <span class="esp-render" data-field="phone_only" style="font-weight:bold;"><?php echo esc_html( $phone_display ); ?></span>
                                    <?php endif; ?>
                                </a>
                            </td>

                            <!-- Website -->
                            <td align="right" valign="middle" style="font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $neutral ); ?>;font-size:16px;white-space:nowrap;">
                                <a href="<?php echo esc_url( $site_url_raw ); ?>" style="color:<?php echo esc_html( $neutral ); ?>;text-decoration:none;font-weight:600;" target="_blank">
                                    <?php if ( $signature_site_img_url ) : ?>
                                        <img src="<?php echo esc_url( $signature_site_img_url ); ?>" alt="<?php echo esc_attr( strtoupper( $site_domain ) ); ?>" style="display:inline-block;max-height:24px;vertical-align:middle;margin-top:-6px;" />
                                    <?php else : ?>
                                        <span class="esp-render" data-field="site"><?php echo esc_html( strtoupper( $site_domain ) ); ?></span>
                                    <?php endif; ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>                     
            <!-- Divider -->
            <tr>
                <td colspan="2" height="1" style="opacity:0.25;line-height:1px;font-size:1px;background:<?php echo esc_html( $neutral ); ?>;margin:0;padding:0;"></td>
            </tr>

            <!-- Social Icons -->
            <tr style="display:block;margin-top:10px;">
                <td colspan="2" style="padding:0;">
                    <?php if ( ! empty( $options['social_links'] ) ) : ?>
                        <div class="social-icons">
                            <?php foreach ( $options['social_links'] as $row ) : ?>
                                <a href="<?php echo esc_url( $row['url'] ); ?>" target="_blank" rel="noopener" style="text-decoration:none;">
                                    <img src="<?php echo esc_url( $row['icon'] ); ?>" alt="" style="width:24px;height:24px;margin-right:6px;" />
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
        $need_render = ( ! $signature_name_img_url || ! $signature_title_img_url || ! $signature_phone_img_url || ! $signature_phone_only_img_url || ! $signature_site_img_url );
        if ( current_user_can( 'edit_post', $post->ID ) && $need_render ) : ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                var renders = document.querySelectorAll('.esp-render');
                if(!renders.length){return;}

                var pending = renders.length;

                renders.forEach(function(el){
                    const field = el.dataset.field;
                    html2canvas(el, {backgroundColor: null}).then(function(canvas){
                        var dataUrl = canvas.toDataURL('image/png');
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
                        var formData = new FormData();
                        formData.append('action', 'esp_upload_signature_image');
                        formData.append('nonce', '<?php echo wp_create_nonce( 'esp_signature_image' ); ?>');
                        formData.append('post_id', '<?php echo (int) $post->ID; ?>');
                        formData.append('field', field);
                        formData.append('image', dataUrl);
                        xhr.onload = function(){
                            if(--pending === 0){ location.reload(); }
                        };
                        xhr.send(formData);
                    });
                });
            });
        </script>
    <?php endif; ?>
    <?php if ( current_user_can( 'read' ) ) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var copyBtn = document.getElementById('esp-copy-btn');
            if(!copyBtn){return;}

            copyBtn.addEventListener('click', function(){
                var sig = document.querySelector('.signature-card');
                if(!sig){return;}
                var html = sig.outerHTML;

                function success(){
                    var original = copyBtn.textContent;
                    copyBtn.textContent = 'Copied!';
                    setTimeout(function(){ copyBtn.textContent = original; }, 2000);
                }

                function fallback(){
                    var ta = document.createElement('textarea');
                    ta.value = html;
                    document.body.appendChild(ta);
                    ta.select();
                    try{ document.execCommand('copy'); } catch(e){}
                    document.body.removeChild(ta);
                    success();
                }

                // Step 1: Try the synchronous execCommand path first (keeps user gesture).
                if (tryExecCommand()) {
                    success();
                    return; // done
                }

                // Step 2: Try modern Clipboard API (may require secure context).
                if (navigator.clipboard && window.ClipboardItem) {
                    const item = new ClipboardItem({
                        'text/html': new Blob([html], { type: 'text/html' }),
                        'text/plain': new Blob([html], { type: 'text/plain' })
                    });

                    navigator.clipboard.write([item]).then(success).catch(function(err){
                        console.error('ClipboardItem error:', err);
                        fallback();
                    });
                } else {
                    // Step 3: Plain-text fallback.
                    fallback();
                }

                // --- helper functions ---
                function tryExecCommand(){
                    var range = document.createRange();
                    range.selectNode(sig);
                    var selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);

                    // Install a one-time copy listener that injects both html and plain text.
                    function onCopy(ev){
                        try {
                            ev.clipboardData.setData('text/html', html);
                            ev.clipboardData.setData('text/plain', html);
                            ev.preventDefault();
                        } catch(copyErr){
                            console.error('clipboardData.setData error:', copyErr);
                        }
                    }
                    document.addEventListener('copy', onCopy, { once: true });

                    var ok = false;
                    try {
                        ok = document.execCommand('copy');
                        if (!ok) {
                            console.warn('execCommand returned false');
                        }
                    } catch (e) {
                        console.error('execCommand error:', e);
                        ok = false;
                    }

                    // Clean up.
                    selection.removeAllRanges();
                    return ok;
                }

            });

            // Reveal copy button once all resources are fully loaded (images, fonts, etc.)
            window.addEventListener('load', function(){
                copyBtn.style.display = 'inline-block';
            });

            // Regenerate button logic.
            (function(){
                var regenBtn = document.getElementById('esp-regenerate-btn');
                if(!regenBtn){return;}

                regenBtn.addEventListener('click', function(){
                    if(!confirm('This will clear cached images and regenerate them. Continue?')){return;}

                    regenBtn.disabled = true;
                    regenBtn.textContent = 'Regenerating…';

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
                    var formData = new FormData();
                    formData.append('action', 'esp_regenerate_signature');
                    formData.append('nonce', '<?php echo wp_create_nonce( 'esp_regenerate_signature' ); ?>');
                    formData.append('post_id', '<?php echo (int) $post->ID; ?>');
                    xhr.onload = function(){
                        location.reload();
                    };
                    xhr.onerror = function(){
                        alert('Error regenerating.');
                        regenBtn.disabled = false;
                    };
                    xhr.send(formData);
                });
            })();
        });
    </script>
    <?php endif; ?>
</body>
</html> 