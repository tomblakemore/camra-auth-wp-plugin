<div class="wrap">
    <h1>CAMRA Auth Settings</h1>
    <form class="form-horizontal" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="camra_auth_branch_code">
                            Branch code
                        </label>
                    </th>

                    <td>
                        <input id="camra_auth_branch_code" name="camra_auth_branch_code" type="text" value="<?php echo form_val(array_get($options, 'branch_code')) ?>" class="regular-text">

                        <p class="description">The short code for the CAMRA branch (leave blank for all branches).</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="camra_auth_extra_memnos">
                            Extra Members
                        </label>
                    </th>

                    <td>
                        <textarea id="camra_auth_extra_memnos" name="camra_auth_extra_memnos" class="regular-text" rows="5"><?php echo form_val(array_get($options, 'extra_memnos')) ?></textarea>

                        <p class="description">One membership number per line.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="camra_auth_key">
                            API Key
                        </label>
                    </th>

                    <td>
                        <input id="camra_auth_key" name="camra_auth_key" type="text" value="<?php echo form_val(array_get($options, 'key')) ?>" class="regular-text">

                        <p class="description">The private key to send in the body of the authentication request.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="camra_auth_url">
                            Service URL
                        </label>
                    </th>

                    <td>
                        <input id="camra_auth_url" name="camra_auth_url" type="text" value="<?php echo form_val(array_get($options, 'url')) ?>" class="regular-text">

                        <p class="description">The URL of the CAMRA service endpoint.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="camra_auth_timeout">
                            Service timeout
                        </label>
                    </th>

                    <td>
                        <input id="camra_auth_timeout" name="camra_auth_timeout" type="text" value="<?php echo form_val(array_get($options, 'timeout')) ?>" class="regular-text">

                        <p class="description">The number of seconds to wait for the service to respond.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        SSL peer verification
                    </th>

                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span>
                                    SSL peer verification
                                </span>
                            </legend>

                            <label for="camra_auth_ssl_verifypeer">
                                <input id="camra_auth_ssl_verifypeer" name="camra_auth_ssl_verifypeer" type="checkbox" class="regular-text" <?php if (array_get($options, 'ssl_verifypeer')): ?>checked<?php endif; ?>>

                                Enable SSL peer verification
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="camra_auth_ssl_trust_certs">
                            SSL cerificate chain path
                        </label>
                    </th>

                    <td>
                        <input id="camra_auth_ssl_trust_certs" name="camra_auth_ssl_trust_certs" type="text" value="<?php echo form_val(array_get($options, 'ssl_trust_certs')) ?>" class="regular-text">

                        <p class="description">The path to the certicate bundle on the server for peer verification.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="hidden" name="option_page" value="camra_auth">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>