<div>
    <h3>PayPal API Settings</h3>
</div>

<div>
    <form method="POST" action="<?php echo site_url('admin/paypal_settings'); ?>">
        <table class="admin-users">
            <tr>
                <th>Name</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>
                    User
                </td>
                <td>
                    <input type="text" name="user" value="<?php echo $ppUser; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    Password
                </td>
                <td>
                    <input type="text" name="password" value="<?php echo $ppPassword; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    Signature
                </td>
                <td>
                    <input type="text" name="signature" value="<?php echo $ppSignature; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    Sandbox Mode
                </td>
                <td>
                    <input type="checkbox" class="styled" name="sandbox_mode" <?php if($isSandbox): ?>checked="checked"<?php endif; ?>>
                </td>
            </tr>
        </table>
        <br>
        <input type="submit" class="black-btn" value="Save">

    </form>
</div>