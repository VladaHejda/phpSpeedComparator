<h1>PHP code speed comparator</h1>

<?php

// predefined settings:
$_predefined_dsn = '';
$_predefined_user = '';
$_predefined_loops = 1000;



if (isset($_POST['compare'])){
    $_eval0 = trim($_POST['eval0']);
    $_eval1 = trim($_POST['eval1']);
    $_eval2 = trim($_POST['eval2']);
    $_loops = (int) $_POST['loops'];
    $_allow_output = isset($_POST['output']);

    if (isset($_POST['connect'])){
        try {
            $db = new PDO($_POST['dsn'], $_POST['user'], $_POST['password']);
            $db->query("SET NAMES 'utf8'");
        } catch (PDOException $e){
            echo '<div style="color:#f00;">'."<h3>Caught PDOException:</h3>\n<p>" . $e->getMessage() . '</p><hr /></div>';
        }
    }

    if ($_allow_output) echo '<h2>Output:</h2>';
    else ob_start();

    if (!empty($_eval0)) eval($_eval0);

    $_iter = $_loops;
    $_start = microtime(TRUE);
    while($_iter--) eval($_eval1);
    $_duration1 = microtime(TRUE) - $_start;

    $_iter = $_loops;
    $_start = microtime(TRUE);
    while($_iter--) eval($_eval2);
    $_duration2 = microtime(TRUE) - $_start;

    if ($_allow_output) echo '<hr />';
    else ob_clean();

    $_color1 = ($_duration1 < $_duration2) ? '#009100' : '#b10000';
    $_color2 = ($_duration1 > $_duration2) ? '#009100' : '#b10000';
    $_faster1 = ($_duration1 < $_duration2) ? ($_duration2 / $_duration1) : '';
    $_faster2 = ($_duration1 > $_duration2) ? ($_duration1 / $_duration2) : '';
}
?>

<form  method="post">
    <p>
        <label><input type="checkbox" name="connect" value="1" <?php echo isset($_POST['connect']) ? 'checked="checked" ' : ''; ?>/> connect to database:</label>
        <label>dsn: <input type="text" name="dsn" size="50" value="<?php echo isset($_POST['dsn']) ? htmlspecialchars($_POST['dsn']) : htmlspecialchars($_predefined_dsn); ?>" /></label>
        <label>user: <input type="text" name="user" value="<?php echo isset($_POST['user']) ? htmlspecialchars($_POST['user']) : $_predefined_user; ?>" /></label>
        <label>password: <input type="password" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" /></label>
    <p>PDO object available in <code>$db</code> variable.</p>
    </p>

    <p>Code common (prepend):<br /><textarea name="eval0" rows="7" cols="100"><?php echo isset($_POST['eval0']) ? htmlspecialchars($_POST['eval0']) : ''; ?></textarea></p>

    <p>Code iterated 1.:<br /><textarea name="eval1" rows="10" cols="100"><?php echo isset($_POST['eval1']) ? htmlspecialchars($_POST['eval1']) : ''; ?></textarea></p>
    <? if (isset($_duration1)){ ?>
        <p style="color: <?php echo $_color1;?>; font-weight: bold;">
            Execution time: <?php echo $_duration1;?> s<br />
            <?php if ($_faster1) echo $_faster1 . ' x faster';?>
        </p>
    <? } ?>

    <p>Code iterated 2.:<br /><textarea name="eval2" rows="10" cols="100"><?php echo isset($_POST['eval2']) ? htmlspecialchars($_POST['eval2']) : ''; ?></textarea></p>
    <? if (isset($_duration2)){ ?>
        <p style="color: <?php echo $_color2;?>; font-weight: bold;">
            Execution time: <?php echo $_duration2;?> s<br />
            <?php  if ($_faster2) echo $_faster2 . ' x faster';?>
        </p>
    <? } ?>

    <p>
        Loops: <input type="text" name="loops" value="<?php echo isset($_loops) ? $_loops : $_predefined_loops; ?>" />
        <label><input type="checkbox" name="output" value="1"<?php echo isset($_POST['output']) ? ' checked="checked"' : '' ;?> /> allow output</label>
        <input type="submit" name="compare" value="compare" />
    </p>
</form>
