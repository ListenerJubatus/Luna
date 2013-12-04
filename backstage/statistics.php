<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: ../login.php");
}

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;


// Show phpinfo() output
if ($action == 'phpinfo' && $pun_user['g_id'] == FORUM_ADMIN)
{
	// Is phpinfo() a disabled function?
	if (strpos(strtolower((string) ini_get('disable_functions')), 'phpinfo') !== false)
		message($lang['PHPinfo disabled message']);

	phpinfo();
	exit;
}


// Get the server load averages (if possible)
if (@file_exists('/proc/loadavg') && is_readable('/proc/loadavg'))
{
	// We use @ just in case
	$fh = @fopen('/proc/loadavg', 'r');
	$load_averages = @fread($fh, 64);
	@fclose($fh);

	if (($fh = @fopen('/proc/loadavg', 'r')))
	{
		$load_averages = fread($fh, 64);
		fclose($fh);
	}
	else
		$load_averages = '';

	$load_averages = @explode(' ', $load_averages);
	$server_load = isset($load_averages[2]) ? $load_averages[0].' '.$load_averages[1].' '.$load_averages[2] : $lang['Not available'];
}
else if (!in_array(PHP_OS, array('WINNT', 'WIN32')) && preg_match('%averages?: ([0-9\.]+),?\s+([0-9\.]+),?\s+([0-9\.]+)%i', @exec('uptime'), $load_averages))
	$server_load = $load_averages[1].' '.$load_averages[2].' '.$load_averages[3];
else
	$server_load = $lang['Not available'];


// Get number of current visitors
$result = $db->query('SELECT COUNT(user_id) FROM '.$db->prefix.'online WHERE idle=0') or error('Unable to fetch online count', __FILE__, __LINE__, $db->error());
$num_online = $db->result($result);


// Collect some additional info about MySQL
if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
{
	// Calculate total db size/row count
	$result = $db->query('SHOW TABLE STATUS LIKE \''.$db->prefix.'%\'') or error('Unable to fetch table status', __FILE__, __LINE__, $db->error());

	$total_records = $total_size = 0;
	while ($status = $db->fetch_assoc($result))
	{
		$total_records += $status['Rows'];
		$total_size += $status['Data_length'] + $status['Index_length'];
	}

	$total_size = file_size($total_size);
}


// Check for the existence of various PHP opcode caches/optimizers
if (function_exists('mmcache'))
	$php_accelerator = '<a href="http://'.$lang['Turck MMCache link'].'">'.$lang['Turck MMCache'].'</a>';
else if (isset($_PHPA))
	$php_accelerator = '<a href="http://'.$lang['ionCube PHP Accelerator link'].'">'.$lang['ionCube PHP Accelerator'].'</a>';
else if (ini_get('apc.enabled'))
	$php_accelerator ='<a href="http://'.$lang['Alternative PHP Cache (APC) link'].'">'.$lang['Alternative PHP Cache (APC)'].'</a>';
else if (ini_get('zend_optimizer.optimization_level'))
	$php_accelerator = '<a href="http://'.$lang['Zend Optimizer link'].'">'.$lang['Zend Optimizer'].'</a>';
else if (ini_get('eaccelerator.enable'))
	$php_accelerator = '<a href="http://'.$lang['eAccelerator link'].'">'.$lang['eAccelerator'].'</a>';
else if (ini_get('xcache.cacher'))
	$php_accelerator = '<a href="http://'.$lang['XCache link'].'">'.$lang['XCache'].'</a>';
else
	$php_accelerator = $lang['NA'];


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Server statistics']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('stats');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Server statistics head'] ?></h3>
    </div>
	<div class="panel-body">
        <table class="table">
            <tr>
                <th class="col-md-2"><?php echo $lang['Server load label'] ?></th>
                <td><?php printf($lang['Server load data']."\n", $server_load, $num_online) ?></td>
            </tr>
            <tr>
                <?php if ($pun_user['g_id'] == FORUM_ADMIN): ?>
                <th><?php echo $lang['Environment label'] ?></th>
                <td>
                    <?php printf($lang['Environment data OS'], PHP_OS) ?><br />
                    <?php printf($lang['Environment data version'], phpversion(), '<a href="statistics.php?action=phpinfo">'.$lang['Show info'].'</a>') ?><br />
                    <?php printf($lang['Environment data acc']."\n", $php_accelerator) ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang['Database label'] ?></th>
                <td>
                    <?php echo implode(' ', $db->get_version())."\n" ?>
                    <?php if (isset($total_records) && isset($total_size)): ?>
                    <br /><?php printf($lang['Database data rows']."\n", forum_number_format($total_records)) ?>
                    <br /><?php printf($lang['Database data size']."\n", $total_size) ?>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
        </table>
	</div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';