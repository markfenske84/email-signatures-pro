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
$office_phone = $options['office_phone'] ?? '';
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

// Format office phone number similarly.
$office_digits  = preg_replace( '/\D+/', '', $office_phone );
$office_display = $office_digits;
if ( 10 === strlen( $office_digits ) ) {
    $office_display = substr( $office_digits, 0, 3 ) . '.' . substr( $office_digits, 3, 3 ) . '.' . substr( $office_digits, 6 );
} elseif ( 11 === strlen( $office_digits ) && '1' === $office_digits[0] ) {
    $office_display = substr( $office_digits, 1, 3 ) . '.' . substr( $office_digits, 4, 3 ) . '.' . substr( $office_digits, 7 );
} elseif ( 7 === strlen( $office_digits ) ) {
    $office_display = substr( $office_digits, 0, 3 ) . '.' . substr( $office_digits, 3 );
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
        .signature-card{background:#fff;max-width:420px;margin:0 auto;padding:20px;}
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
    <div class="signature-card" style="background:#ffffff;max-width:420px;padding:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr style="display:block;margin-bottom:10px;">
                <!-- Avatar -->
                <td width="100" class="signature-avatar" valign="top" align="left" style="padding-right:20px;">
                    <?php if ( $avatar_url ) : ?>
                        <img src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar" style="width:100px;height:100px;border-radius:50%;object-fit:cover;" />
                    <?php elseif( ! empty( $options['default_avatar'] ) ) : ?>
                        <img src="<?php echo esc_url( $options['default_avatar'] ); ?>" alt="Avatar" style="width:100px;height:100px;border-radius:50%;object-fit:cover;" />
                    <?php endif; ?>
                </td>

                <!-- Main details -->
                <td valign="top" align="left" style="padding-top:5px;">
                    <?php if ( $signature_name_img_url ) : ?>
                        <img src="<?php echo esc_url( $signature_name_img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" style="display:block;margin:0;" />
                    <?php else : ?>
                        <p class="signature-name esp-render" data-field="name" style="margin:0;display:inline-block;white-space:nowrap;line-height:1;font-size:29px;"><?php echo esc_html( get_the_title() ); ?></p>
                    <?php endif; ?>

                    <?php if ( $signature_title_img_url ) : ?>
                        <img src="<?php echo esc_url( $signature_title_img_url ); ?>" alt="<?php echo esc_attr( $job_title ); ?>" style="display:block;margin:8px 0 16px;" />
                    <?php else : ?>
                        <p class="signature-title esp-render" data-field="title" style="margin:6px 0 16px;font-size:15px;font-weight:400;display:inline-block;white-space:nowrap;line-height:1;"><?php echo esc_html( $job_title ); ?></p>
                    <?php endif; ?>

                    <!-- CTA + Phone -->
                    <table cellpadding="0" cellspacing="0" class="signature-cta-line">
                        <tr>
                            <?php if ( $cta_button ) : ?>
                                <td class="signature-cta" style="padding-right:18px;">
                                    <a href="<?php echo esc_url( $meeting_url ); ?>" target="_blank"><img src="<?php echo esc_url( $cta_button ); ?>" alt="CTA" style="display:block;height:32px;" /></a>
                                </td>
                            <?php endif; ?>
                            <td style="font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $secondary ); ?>;font-size:15px;white-space:nowrap;">
                                <a href="tel:<?php echo esc_attr( $phone_digits ); ?>" style="color:<?php echo esc_html( $secondary ); ?>;text-decoration:none;font-weight:500;display:inline-block;line-height:0;vertical-align:middle;margin-top:-4px;">
                                    <?php if ( $signature_phone_img_url ) : ?>
                                        <img src="<?php echo esc_url( $signature_phone_img_url ); ?>" alt="<?php echo esc_attr( 'M ' . $phone_display ); ?>" style="display:inline-block;max-height:20px;vertical-align:middle;" />
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
                <td colspan="2" height="1" style="line-height:1px;font-size:1px;background:#E4E5E7;margin:0;padding:0;"></td>
            </tr>

            <!-- Bottom row -->
            <tr class="signature-bottom">
                <td colspan="2">
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 6px 0;">
                        <tr>
                            <!-- Company Logo -->
                            <td style="padding-right:20px;white-space:nowrap;">
                                <?php if ( $company_logo ) : ?>
                                    <a href="<?php echo esc_url( $site_url_raw ); ?>" target="_blank" style="display:inline-block;">
                                        <img src="<?php echo esc_url( $company_logo ); ?>" alt="Company Logo" style="vertical-align:middle;" />
                                    </a>
                                <?php endif; ?>
                            </td>

                            <!-- Phone bottom -->
                            <td style="font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $neutral ); ?>;font-size:14px;white-space:nowrap;vertical-align:baseline;padding-top:2px;">
                                <a href="tel:<?php echo esc_attr( $office_digits ); ?>" style="color:<?php echo esc_html( $neutral ); ?>;text-decoration:none;font-weight:400;display:inline-block;margin-right:2px;line-height:0;vertical-align:middle;">
                                    <?php if ( $signature_phone_only_img_url ) : ?>
                                        <img src="<?php echo esc_url( $signature_phone_only_img_url ); ?>" alt="<?php echo esc_attr( 'O ' . $office_display ); ?>" style="display:inline-block;max-height:20px;vertical-align:middle;" />
                                    <?php else : ?>
                                        <span class="esp-render" data-field="phone_only" style="font-weight:bold;"><small style="font-weight:400;">O</small> <?php echo esc_html( $office_display ); ?></span>
                                    <?php endif; ?>
                                </a>
                            </td>

                            <!-- Website -->
                            <td align="right" style="font-family:<?php echo esc_html( $body_css ); ?>;color:<?php echo esc_html( $neutral ); ?>;font-size:14px;white-space:nowrap;vertical-align:baseline;">
                                <a href="<?php echo esc_url( $site_url_raw ); ?>" style="color:<?php echo esc_html( $neutral ); ?>;text-decoration:none;font-weight:500;display:inline-block;line-height:0;vertical-align:middle;" target="_blank">
                                    <?php if ( $signature_site_img_url ) : ?>
                                        <img src="<?php echo esc_url( $signature_site_img_url ); ?>" alt="<?php echo esc_attr( strtoupper( $site_domain ) ); ?>" style="display:inline-block;max-height:20px;vertical-align:middle;" />
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
                <td colspan="2" height="1" style="line-height:1px;font-size:1px;background:#E4E5E7;margin:0;padding:0;"></td>
            </tr>

            <!-- Social Icons -->
            <tr style="display:block;margin-top:10px;">
                <td colspan="2" style="padding:0;">
                    <?php if ( ! empty( $options['social_links'] ) ) : ?>
                        <div class="social-icons">
                            <?php foreach ( $options['social_links'] as $row ) : ?>
                                <a href="<?php echo esc_url( $row['url'] ); ?>" target="_blank" rel="noopener" style="text-decoration:none;display:inline-block;">
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

                // Utility: trim transparent whitespace from a canvas so resulting PNG has no extra padding.
                function trimCanvas(canvas){
                    var ctx = canvas.getContext('2d');
                    var width = canvas.width;
                    var height = canvas.height;

                    // Get pixel data (alpha channel only) and determine bounding box of non-transparent pixels.
                    var imgData = ctx.getImageData(0, 0, width, height).data;
                    var top = height, left = width, right = 0, bottom = 0;

                    for(var y = 0; y < height; y++){
                        for(var x = 0; x < width; x++){
                            var alpha = imgData[(y * width + x) * 4 + 3]; // alpha channel
                            if(alpha !== 0){
                                if(x < left) { left = x; }
                                if(x > right){ right = x; }
                                if(y < top)  { top = y; }
                                if(y > bottom){ bottom = y; }
                            }
                        }
                    }

                    // If nothing found, return original canvas.
                    if(right - left <= 0 || bottom - top <= 0){
                        return canvas;
                    }

                    var trimmedWidth  = right - left + 1;
                    var trimmedHeight = bottom - top + 1;
                    var trimmed = document.createElement('canvas');
                    trimmed.width  = trimmedWidth;
                    trimmed.height = trimmedHeight;
                    trimmed.getContext('2d').drawImage(canvas, left, top, trimmedWidth, trimmedHeight, 0, 0, trimmedWidth, trimmedHeight);
                    return trimmed;
                }

                renders.forEach(function(el){
                    const field = el.dataset.field;
                    // Capture at a higher pixel density for extra clarity.
                    // We multiply the current devicePixelRatio to create an over-sampling effect,
                    // ensuring the small final images (≈20px tall) remain razor-sharp.
                    var dpr   = window.devicePixelRatio || 1;
                    // Render at 1× the devicePixelRatio (results in natural-sized PNGs).
                    // Example: DPR 2 → scale 2; DPR 1 → scale 1.
                    var scale = dpr;
                    html2canvas(el, {backgroundColor: null, scale: scale}).then(function(canvas){
                        // Remove any transparent whitespace before encoding.
                        canvas = trimCanvas(canvas);
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

            // Reveal copy button once all assets are fully loaded.
            window.addEventListener('load', function(){
                copyBtn.style.display = 'inline-block';

                // ------------------------------------------------------------------
                // Ensure all <img> elements inside the signature have explicit width
                // and height attributes so that email clients (which often strip
                // inline styles) render them at the correct size instead of their
                // full natural resolution.
                // ------------------------------------------------------------------
                var imgs = document.querySelectorAll('.signature-card img');
                imgs.forEach(function(img){
                    // Skip if attributes already present.
                    if(img.hasAttribute('width') || img.hasAttribute('height')){ return; }

                    // Use the element's rendered size as the desired dimension.
                    var rect = img.getBoundingClientRect();
                    if(rect.width && rect.height){
                        img.setAttribute('width',  Math.round(rect.width));
                        img.setAttribute('height', Math.round(rect.height));
                    }
                });
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