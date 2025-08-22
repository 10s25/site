// Nom du RS selon rs_icon
<?php
$rsFromIcon = [
'bluesky'   => 'Bluesky',
'facebookp' => 'Facebook (pages)',
'facebookg' => 'Facebook (groupes)',
'instagram' => 'Instagram',
'piaille'   => 'Piaille',
'signal'    => 'Signal',
'telegram'  => 'Telegram',
'tiktok'    => 'TikTok',
'twitter'   => 'X (Twitter)',
]; ?>
<ul class="sidebar-section-1">
<?php foreach ($groups as $cat_name => $cat_array) { ?>
	<li>
		<h3 class="font-yellow"><?php echo $cat_name; ?></h3>
		<ul class="sidebar-section-2">
<?php foreach ($cat_array as $nom =>$liste_groupes) { ?>
			<li>
				<?php if ($nom != '') echo "<p class=\"font-yellow\">$nom</p>"; ?>
				<ul>
<?php foreach ($liste_groupes['item'] as $rs_icon => $liste_rs) { ?>
					<li class="<?php echo $rs_icon ; ?>">
						<p><?php echo $rsFromIcon[$rs_icon]; ?></p>
						<ul>
<?php foreach ($liste_rs as $link1) { ?>
							<li>
								<a href="<?php echo $link1['url'] ; ?>" target="_blank" rel="me"><?php echo $link1['nom_rs'] ; ?></a>
							</li>
<?php } ?>
						</ul>
					</li>
<?php if(isset($liste_groupes['children'])) {
	foreach ($liste_groupes['children'] as $nom => $children) {
?>
					<li>
						<?php if ($nom != '') echo "<p class=\"font-yellow\">$nom</p>"; ?>
						<ul>
<?php foreach ($children as $rs_icon => $list_children_rs) { ?>
							<li class="child <?php echo $rs_icon ; ?>">
								<p><?php echo $rsFromIcon[$rs_icon]; ?></p>
								<ul>
<?php foreach ($list_children_rs as $link2) { ?>
									<li>
										<a href="<?php echo $link2['url'] ; ?>" target="_blank" rel="me"><?php echo $link2['nom_rs'] ; ?></a>
									</li>
<?php } ?>
								</ul>
							</li>
<?php } ?>
						</ul>
					</li>
<?php }} ?>
<?php } ?>
				</ul>
			</li>
<?php } ?>
		</ul>
	</li>
<?php } ?>
</ul>

