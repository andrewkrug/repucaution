<h2>Hello, <?php echo $user->username;?></h2>
<p>
    Your account was <?php echo ($user->active) ? 'unblocked' : 'blocked';?> by administrator!
</p>
