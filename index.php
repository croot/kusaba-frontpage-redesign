<?php 
	require_once('config.php');
	require_once('assets/funcoes.php');
	require_once('inc/functions.php');
	require_once(KU_ROOTDIR . 'inc/classes/bans.class.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>xchan</title>
		<link rel="stylesheet" type="text/css" href="assets/front.css">
	</head>

	<body>
		<div id="logo"></div>

		<div id="wrapper">

			<div id="menu">
				<ul>
					<li><a href="./" <?php if (!isset($_GET['p']) || $_GET['p'] == '') { echo 'id="active"'; } ?>>Home</a></li>
					<li><a href="?p=news" <?php if (isset($_GET['p']) && $_GET['p'] == 'news') { echo 'id="active"'; } ?>>News</a></li>
					<li><a href="?p=faq" <?php if (isset($_GET['p']) && $_GET['p'] == 'faq') { echo 'id="active"'; } ?>>F.A.Q.</a></li>
					<li><a href="?p=rules" <?php if (isset($_GET['p']) && $_GET['p'] == 'rules') { echo 'id="active"'; } ?>>Regras</a></li>
					<li><a href="?p=banlist" <?php if (isset($_GET['p']) && $_GET['p'] == 'banlist') { echo 'id="active"'; } ?>>Bans</a></li>
				</ul>
			</div>

			<?php
			if (!isset($_GET['p']) || $_GET['p'] == '') { ?>
				<div id="box">
				<div id="boxheader"><h2>Boards</h2></div>
				<div id="boxcontent">
					<?php
						$sections = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "sections` ORDER BY `order` ASC");
						foreach ($sections as $key=>$section) {
							echo "<div id='column'>
									<h2>".$section['name']."</h2>";
							$results = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "boards` WHERE `section` = '" . $section['id'] . "' ORDER BY `order` ASC, `name` ASC");
							echo "<ul>";
							foreach ($results as $line) {
									echo "<li><a href='".$line['name']."/'>/".$line['name']."/ - ".$line['desc']."</a></li>";
							}
							echo "</ul>
							</div>";
						}
					?>
					<div id="column">
						<h2>Outros</h2>
						<ul>
							<!--<li><a href="http://bbs.xchan.info/"><strike>xchan BBS</strike></a></li>
							<li><a href="http://advice.xchan.info/">Advice Generator</a></li>
							<li><a href="http://tracker.xchan.info">Torrent Tracker</a></li>
							<li><a href="/irc.html" title="#xchan @ irc.rizon.net">IRC</a></li>-->
						</ul>
					</div>
					<br style="clear: left;" />
				</div>
			</div>

			<div id="box">
					<?php
						$news = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 0 ORDER BY `timestamp` DESC LIMIT 0,1");
						echo "<span style='float: right; margin-top: 2px; margin-right: 5px;'>(<a href='?p=news&id=".$news[0]['id']."'>#permalink</a>)</span><div id='boxheader'><h2>".$news[0]['subject']." - ".date("d/m/Y \@ H:i:s",$news[0]['timestamp'])."</h2></div>
						<div id='boxcontent'>";
						echo "<p>".$news[0]['message']."</p>";
					?>
				</div>
			</div>

			<div id="inside">

				<div id="box2" style="width: 400px; float: left; padding-bottom: 0px;">
					<div id="boxheader"><h2>Recent Imagens</h2></div>
					<div id="boxcontent" class="recent-images">
							<?php
								$img = $tc_db->GetAll("SELECT * FROM ".KU_DBPREFIX."posts WHERE (`file_type` = 'jpg' OR `file_type` = 'gif' OR `file_type` = 'png') AND `IS_DELETED` = 0 AND boardid!=12 AND boardid!=13 ORDER BY `timestamp` DESC LIMIT 0,10");

								foreach ($img as $i) { 
									if ($i['thumb_h'] > 140) { $h = 140; }
									else { $h = $i['thumb_h']; }
									if ($i['parentid'] == 0) { $iid = $i['id']; }
									else { $iid = $i['parentid']; }
									$b = id2board($i['boardid']);
									echo "<li><a href='".$b."/res/".$iid.".html#".$i['id']."'><img src='".$b."/thumb/".$i['file']."s.".$i['file_type']."' height='".$h."' /></a></li>";
								}
							?>
					</div>
				</div>

				<div id="box2" style="width: 480px; float: right;">
					<div id="boxheader"><h2>Recent Posts</h2></div>
					<div id="boxcontent">
							<?php
								$res = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "posts` WHERE `IS_DELETED` = 0 AND boardid!=12 AND boardid!=13 ORDER BY timestamp DESC LIMIT 0,7");
								foreach ($res as $r) { 
									$b = id2board($r['boardid']);
									echo date("d/m \@ H:i",$r['timestamp'])." - <a href='".$b."/'>/".$b."/</a> - <a href='".$b."/res/".($r['parentid'] == 0?$r['id']:$r['parentid']).".html#".$r['id']."'>#".$r['id']."</a> - ".cut($r['message'],50)."<br />";
								}
							?>
					</div>
				</div>

				<div id="box2" style="width: 480px; float: right;">
					<div id="boxheader"><h2>Popular Threads</h2></div>
					<div id="boxcontent">
							<?php
								$res = $tc_db->GetAll("SELECT *,count(parentid) as replies FROM ".KU_DBPREFIX."posts WHERE IS_DELETED=0 AND parentid!=0 AND boardid!=12 AND boardid!=13 GROUP BY parentid ORDER BY replies DESC LIMIT 0,7");
								foreach ($res as $r) { 
									$q = $tc_db->GetAll("SELECT * FROM ".KU_DBPREFIX."posts WHERE id='".$r['parentid']."' AND boardid='".$r['boardid']."'");
									$b = id2board($q[0]['boardid']);
									echo date("d/m \@ H:i",$q[0]['timestamp'])." - <a href='".$b."/'>/".$b."/</a> - <a href='".$b."/res/".$q[0]['id'].".html#".$q[0]['id']."' title='Respostas:  ".$r['replies']."'>#".$q[0]['id']."</a> - ".cut($q[0]['message'],50)."<br />";
								}
							?>
					</div>
				</div>

				<div id="box2" style="width: 480px; float: right;">
					<div id="boxheader"><h2>Stats</h2></div>
					<div id="boxcontent">
							<?php 
								$res = $tc_db->GetAll("SELECT id FROM `" . KU_DBPREFIX . "posts` WHERE `IS_DELETED` = 0 AND `file` != ''");
								echo "Imagens: ".count($res)."<br />";
								$res = $tc_db->GetAll("SELECT id FROM `" . KU_DBPREFIX . "posts` WHERE `IS_DELETED` = 0");
								echo "Posts Ativos: ".count($res)."<br />";
								$res = $tc_db->GetAll("SELECT sum(file_size) FROM `" . KU_DBPREFIX . "posts` WHERE `IS_DELETED` = 0 AND `file` != ''");
								$s = fileSizeInfo($res[0][0]);
								echo "Espaço em disco: ".$s[0]." ".$s[1]."<br />";
							?>
					</div>
				</div>

			</div>

			<br style="clear: left" />

			<?php } 
			elseif ($_GET['p'] == 'news') {
				if (!isset($_GET['id']) || $_GET['id'] == '') {
					$news = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 0 ORDER BY `timestamp` DESC");
					foreach ($news as $n) {
						echo "<div id='box'>
								<span style='float: right; margin-top: 2px; margin-right: 5px;'>(<a href='?p=news&id=".$n['id']."'>#permalink</a>)</span>
								<div id='boxheader'><h2>".$n['subject']." - ".date("d/m/Y \@ H:i:s",$n['timestamp'])."</h2></div>
								<div id='boxcontent'>".$n['message']."</div>
							  </div>";
					}
				} else {
					$news = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 0 AND id = ".(int)$_GET['id']." ORDER BY `timestamp` DESC");
					echo "<div id='box'>
							<div id='boxheader'><h2>".$news[0]['subject']." - ".date("d/m/Y \@ H:i:s",$news[0]['timestamp'])."</h2></div>
							<div id='boxcontent'>".$news[0]['message']."</div>
						  </div>";
				}

			}

			elseif ($_GET['p'] == 'faq') { 
				echo "<div id='inside'>
							<div id='box'>
								<div id='boxheader'><h2>FAQ</h2></div>
								<div id='boxcontent'>Welcome to our FAQ.</div>
							</div>
							<div id='box2' style='width: 350px; float: left;'>
								<div id='boxheader'><h2>Questions</h2></div>
								<div id='boxcontent'><ul>";

				$faq = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 1 ORDER BY `order` ASC");
				foreach ($faq as $f) {
					++$i;
					echo "<li><a href='#r-".$i."'>".$f['subject']."</a></li>";
				}
								
				echo "</ul></div>
							</div>
							<div id='box2' style='width: 530px; float: right;'>
								<div id='boxheader'><h2>Replies</h2></div>
								<div id='boxcontent'>";
				
				foreach ($faq as $f) {
					++$ii;
					echo "<h3 id='r-".$ii."'>".$f['subject']."</h3>".$f['message']."<hr />";
				}
								
				echo "</div>
							</div>
					  </div>
					  <br style='clear: left' />";			
			}

			elseif ($_GET['p'] == 'rules') {
				echo "<div id='box'>
								<div id='boxheader'><h2>Rules</h2></div>
								<div id='boxcontent'><ol>";

				$regras = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 2 ORDER BY `order` ASC");
				foreach ($regras as $r) {
					echo "<li>".$r['message']."</li>";
				}
								
				echo "</ol></div>
							</div>";
			}

			// elseif ($_GET['p'] == 'custom') { }

			elseif ($_GET['p'] == 'banlist') {
				?>
		
				<table>
					<theader>
						<tr>
							<th>IP</th>
							<th>Reason</th>
							<th>Boards</th>
							<th>Banned at</th>
							<th>Expires at</th>
							<th>Moderator</th>
						</tr>
					</theader>
					<tbody>
						<?php
						$bans = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "banlist` ORDER BY `at` DESC LIMIT 0,25");
						foreach ($bans as $b) {
							echo "<tr>
									<td><a href='http://www.geoiptool.com/pt/?IP=".md5_decrypt($b['ip'],KU_RANDOMSEED)."'>".md5_decrypt($b['ip'],KU_RANDOMSEED)."</a></td>
									<td>".$b['reason']."</td>
									<td>".($b['boards'] == '' ? 'Todas':$b['boards'])."</td>
									<td>".date("d/m/Y", $b['at'])."</td>
									<td>".($b['until'] > 0 ? date("d/m/Y", $b['until']) : 'Never')."</td>
									<td>".str_replace("board.php","Skynet",$b['by'])."</td>
								</tr>";
						}
						?>
					</tbody>
					<tfooter>
					</tfooter>
				</table>
			
				<?php
			}

			elseif ($_GET['p'] == 'contact') {
				?>
				<div id="box">
					<div id='boxheader'><h2>Contato</h2></div>
					<div id='boxcontent'>Em caso de duvidas, criticas ou sugestões favor entrar em contato@xchan.info<br />Bans não serão tratados via email e nem em lugar algum, se você está banido simplesmente espere o ban expirar.</div>
				</div>
				<?php
			}
 
			?>

			<div id="footer">Copyright &copy; 2009-<?php echo date("Y"); ?> xchan.info - Some rights reserved<br /><a href="kusaba.php" target="_page">- Versão com Frames -</a></div>

		</div>

	</body>

</html>