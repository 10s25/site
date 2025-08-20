<ul class="sidebar-section-1">
<?php foreach ($groups as $cat_name => $cat_array) { ?>
	<li>
		<h3 class="font-yellow"><?php echo htmlspecialchars($cat_name, ENT_QUOTES, 'UTF-8'); ?></h3>
		<ul class="sidebar-section-2">
<?php foreach ($cat_array as $nom =>$liste_groupes) { ?>
			<li>
				<?php if ($nom != '') echo "<p class=\"font-yellow\">" . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . "</p>"; ?>
				<ul>
<?php foreach ($liste_groupes['item'] as $rs_icon => $liste_rs) { ?>
					<li class="<?php echo htmlspecialchars($rs_icon, ENT_QUOTES, 'UTF-8'); ?>">
						<p><?php echo htmlspecialchars($rsFromIcon[$rs_icon], ENT_QUOTES, 'UTF-8'); ?></p>
						<ul>
<?php foreach ($liste_rs as $link) { ?>
							<li>
								<a href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="me"><?php echo htmlspecialchars($link['nom_rs'], ENT_QUOTES, 'UTF-8'); ?></a>
							</li>
<?php } ?>
						</ul>
					</li>
<?php if(count($liste_groupes['children'])) {
	foreach ($liste_groupes['children'] as $nom => $children) {
		$children = sort_by_field($children);
?>
					<li>
						<?php if ($nom != '') echo "<p class=\"font-yellow\">" . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . "</p>"; ?>
						<ul>
<?php foreach ($children as $rs_icon => $link) { ?>
							<li class="child <?php echo htmlspecialchars($rs_icon, ENT_QUOTES, 'UTF-8'); ?>">
								<p><?php echo htmlspecialchars($rsFromIcon[$rs_icon], ENT_QUOTES, 'UTF-8'); ?></p>
								<a href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="me"><?php echo htmlspecialchars($link['nom_rs'], ENT_QUOTES, 'UTF-8'); ?></a>
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

