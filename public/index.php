<?php

if (!class_exists('Dotenv\Dotenv')) {
    // Correct the path to point to the vendor directory
    require_once __DIR__ . '/../vendor/autoload.php'; // Adjust the path as necessary

    // Initialize and load dotenv
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Adjust the path as necessary
    $dotenv->load();
}

function sanitize_inputs($request)
{
    $sanitized = [];
    foreach ($request as $key => $value) {
        $sanitized[$key] = sanitize_text_field($value);
    }
    return $sanitized;
}

function generate_code($first_name, $last_name)
{
    return strtoupper($first_name . $last_name . rand(10, 99));
}

if (isset($_POST['submit_member_form']) && wp_verify_nonce($_POST['member_form_nonce_field'], 'member_form_nonce')) {
    $data = sanitize_inputs($_POST);

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        wp_die('Please enter a valid email address.');
    }

    $referral_code = generate_code($data['first_name'], $data['last_name']);

    $social_media_links_string = $data['facebook'] . ',' . $data['instagram'] . ',' . $data['twitter'] . ',' . $data['youtube'] . ',' . $data['tiktok'] . ',' . $data['linkedin'];

    $curl = curl_init();

    $api_key = $_ENV['REFERRAL_ROCK_BASIC_AUTH_KEY'];
    $program_key = $_ENV['REFERRAL_ROCK_PROGRAM_ID'];

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.referralrock.com/api/members',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
                "programId": "' . $program_key . '",
                "firstName": "' . $data['first_name'] . '",
                "lastName": "' . $data['last_name'] . '",
                "email": "' . $data['email'] . '",
                "referralCode": "' . $referral_code . '",
                "phone": "' . $data['phone_number'] . '",
                "disabledFlag": true,
                "customOption1Name": "Social Media Links",
                "customOption1Value": "' . $social_media_links_string . '"
            }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $api_key . ''
        )
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $response = json_decode($response, false);

    $redirect_url = wp_get_referer();

    if ($redirect_url) {
        wp_safe_redirect($redirect_url);
        exit;
    } else {
        wp_redirect(home_url());
    }
}
?>
<div id="app">
    <?php
    $fields = [
        [
            'name' => 'first_name',
            'placeholder' => 'First Name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'last_name',
            'placeholder' => 'Last Name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'email',
            'placeholder' => 'Email',
            'id' => 'email',
            'type' => 'email',
            'value' => '',
        ],
        [
            'name' => 'phone_number',
            'placeholder' => 'Phone',
            'id' => 'phone_number',
            'type' => 'tel',
            'value' => '',
        ],
    ];

    $socialMediaFields = [
        [
            'name' => 'facebook',
            'placeholder' => 'Facebook URL',
            'id' => 'facebook',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'instagram',
            'placeholder' => 'Instagram URL',
            'id' => 'instagram',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'twitter',
            'placeholder' => 'Twitter URL',
            'id' => 'twitter',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'youtube',
            'placeholder' => 'YouTube URL',
            'id' => 'youtube',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'tiktok',
            'placeholder' => 'TikTok URL',
            'id' => 'tiktok',
            'type' => 'text',
            'value' => '',
        ],
        [
            'name' => 'linkedin',
            'placeholder' => 'LinkedIn URL',
            'id' => 'linkedin',
            'type' => 'text',
            'value' => '',
        ],
    ];
    ?>
    <form action="" method="post">
        <?= wp_nonce_field('member_form_nonce', 'member_form_nonce_field'); ?>
        <div class="card bg-white rounded-none shadow-lg px-4 lg:px-8 py-5">
            <div class="card-body space-y-1">
                <h1 class="text-3xl text-neutral-700 font-bold uppercase italic mb-5">Tell us about your brand!</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4" style="gap: .75rem">
                    <?php
                    foreach ($fields as $field) {
                        echo '<div class="form-control w-full">';
                        echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" class="input input-bordered w-full placeholder:text-sm shadow-sm" value="' . (isset($_POST[$field['name']]) ? esc_attr($_POST[$field['name']]) : '') . '" style="border-radius: 5px; box-shadow: inset 1px 2px 0 rgba(0,0,0,.06);" />';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3" style="gap: .75rem">
                    <?php

                    foreach ($socialMediaFields as $field) {
                        echo '<div class="form-control w-full">';
                        echo '<input type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" class="input input-bordered w-full  placeholder:text-sm shadow-sm" value="' . (isset($_POST[$field['name']]) ? esc_attr($_POST[$field['name']]) : '') . '" style="border-radius: 5px; box-shadow: inset 1px 2px 0 rgba(0,0,0,.06);" />';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="flex justify-center">
                    <button type="submit" name="submit_member_form" class="btn bg-desp-green text-[#425766] w-64 hover:bg-desp-green hover:text-white text-xl capitalize" style="letter-spacing: 1px;">Submit</button>
                </div>

                <div style="padding-top: 2.5rem">
                    <p style="font-size: xx-small;">Disclaimer: By submitting this form, I am giving Discount Extended Service Plans consent to contact me by email and/or telephone which may include artificial or pre-recorded calls and/or text messages, delivered via automated technology at the telephone number(s) provided above even if I am on a corporate, state or national Do Not Call Registry. I understand that consent is not a condition of purchase. For SMS messaging, text STOP to stop. Msg and data rates may apply. Max 10 messages per month. The Discount Extended Service Plans Privacy Policy governs our Data Collection Policy.</p>
                </div>
            </div>
        </div>
    </form>
</div>