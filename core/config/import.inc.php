<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		tbg_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		$charset = TBGContext::getI18n()->getCharset();
		if (TBGContext::getRequest()->getParameter('step'))
		{
			switch (TBGContext::getRequest()->getParameter('step'))
			{
				case 1:
					?>
					<table style="width: 100%" cellpadding=0 cellspacing=0>
						<tr>
						<td style="padding-right: 10px;">
							<table class="configstrip" cellpadding=0 cellspacing=0>
								<tr>
									<td class="cleft"><b><?php echo __('Import data from version 1.9'); ?></b></td>
									<td class="cright">&nbsp;</td>
								</tr>
								<tr>
									<td colspan=2 class="cdesc">
									<?php echo __('Checking connection details ... '); ?>
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					<div style="padding: 5px; width: 450px;">
					<?php
					
					$b1_hostname = TBGContext::getRequest()->getParameter('b1_hostname', null, false);
					$b1_database = TBGContext::getRequest()->getParameter('b1_database', null, false);
					$b1_username = TBGContext::getRequest()->getParameter('b1_username', null, false);
					$b1_password = TBGContext::getRequest()->getParameter('b1_password', null, false);
					
					B2DB::closeDBLink();
					B2DB::setHost($b1_hostname);
					B2DB::setDBname($b1_database);
					B2DB::setUname($b1_username);
					B2DB::setPasswd($b1_password);
					try
					{
						B2DB::doConnect();
						echo __('Connection details OK!');
						echo '<br><br><b>';
						echo __('Please select what to import:');
						echo '</b><br>';
						
						$sql = 'select * from paks';
						$res = B2DB::simpleQuery($sql);
						
						?>
						<form accept-charset="<?php echo $charset; ?>" action="config.php" enctype="multipart/form-data" method="post" name="importdataform">
						<input type="hidden" name="module" value="core">
						<input type="hidden" name="section" value="16">
						<input type="hidden" name="step" value="2">
						<input type="checkbox" name="import_inactive_users" value="1" checked><label for="import_inactive_users"><?php echo __('Import unactivated users'); ?></label><br>
						<input type="checkbox" name="import_active_users" value="1" checked><label for="import_active_users"><?php echo __('Import activated users'); ?></label><br>
						<br>
						<input type="hidden" name="b1_hostname" value="<?php echo $b1_hostname; ?>">
						<input type="hidden" name="b1_database" value="<?php echo $b1_database; ?>">
						<input type="hidden" name="b1_username" value="<?php echo $b1_username; ?>">
						<input type="hidden" name="b1_password" value="<?php echo $b1_password; ?>">
						<?php
						
						while ($row = $res->fetch_array())
						{
							?>
							<div style="font-weight: bold; font-size: 14px;"><?php echo $row['pak_desc']; ?></div>
							<input type="checkbox" name="projects[<?php echo $row['id']; ?>][import_project]" value="1" checked><label for="projects[<?php echo $row['id']; ?>][import_project]"><?php echo __('Import this project'); ?></label><br>
							<input type="checkbox" name="projects[<?php echo $row['id']; ?>][import_editions]" value="1" checked><label for="projects[<?php echo $row['id']; ?>][import_editions]"><?php echo __('Import editions and builds from this project'); ?></label><br>
							<input type="checkbox" name="projects[<?php echo $row['id']; ?>][import_tbg_]" value="1" checked><label for="projects[<?php echo $row['id']; ?>][import_tbg_]"><?php echo __('Import bug reports from this project'); ?></label><br>
							<input type="checkbox" name="projects[<?php echo $row['id']; ?>][import_comments]" value="1" checked><label for="projects[<?php echo $row['id']; ?>][import_comments]"><?php echo __('Import comments from bug reports'); ?></label><br>
							<br>
							<?php
						}
						
						?>
						<p style="float: left;"><b><?php echo __('When you are ready to continue, click "Next"'); ?></b></p>
						<input type="submit" value="<?php echo __('Next'); ?> &gt;&gt;" style="float: right;">
						</form>
						</div><?php
					}
					catch (Exception $e)
					{
						echo '<b>' . __('An error occured when validation the connection details: %error%', array('%error%' => '</b><br><i>' . $e->getMessage() . '</i>'));
						echo '<br><a href="#" onclick="history.go(-1);"><b>&lt;&lt; ' . __('Back') . '</b></a>';
					}
					break;
				case 2:
					?>
					<table style="width: 100%" cellpadding=0 cellspacing=0>
						<tr>
						<td style="padding-right: 10px;">
							<table class="configstrip" cellpadding=0 cellspacing=0>
								<tr>
									<td class="cleft"><b><?php echo __('Import data from version 1.9'); ?></b></td>
									<td class="cright">&nbsp;</td>
								</tr>
								<tr>
									<td colspan=2 class="cdesc">
									<?php echo __('Import starting'); ?> ...<br>
									<?php echo __('Please wait, this might take a while'); ?> ... <br>
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					<div style="padding: 5px; width: 450px;">
					<?php 
					
					$orig_hostname = B2DB::getHost();
					$orig_database = B2DB::getDBname();
					$orig_username = B2DB::getUname();
					$orig_password = B2DB::getPasswd();
					$orig_dbtype = B2DB::getDBtype();

					$b1_hostname = TBGContext::getRequest()->getParameter('b1_hostname', null, false);
					$b1_database = TBGContext::getRequest()->getParameter('b1_database', null, false);
					$b1_username = TBGContext::getRequest()->getParameter('b1_username', null, false);
					$b1_password = TBGContext::getRequest()->getParameter('b1_password', null, false);
					
					//var_dump($b1_hostname, $b1_database, $b1_username, $b1_password);
					
					if (TBGContext::getRequest()->getParameter('import_inactive_users') || TBGContext::getRequest()->getParameter('import_active_users'))
					{
						echo __('Importing users') . ' ... ';
						flush();
						ob_flush();
						
						B2DB::closeDBLink();
						B2DB::setHost($b1_hostname);
						B2DB::setDBname($b1_database);
						B2DB::setUname($b1_username);
						B2DB::setPasswd($b1_password);
						B2DB::setDBtype('mysql');
						B2DB::doConnect();
						$sql = 'select * from userstable where id > 2';
						$res = B2DB::simpleQuery($sql);
						B2DB::closeDBLink();
						B2DB::setHost($orig_hostname);
						B2DB::setDBname($orig_database);
						B2DB::setUname($orig_username);
						B2DB::setPasswd($orig_password);
						B2DB::setDBtype($orig_dbtype);
						B2DB::doConnect();
						while ($row = $res->fetch_array())
						{
							B2DB::getTable('TBGUsersTable')->doDeleteById($row['id']);
							if ($row['activated'] == 1 && TBGContext::getRequest()->getParameter('import_active_users'))
							{
								$user = TBGUser::createNew($row['uname'], $row['realname'], $row['realname'], TBGContext::getScope()->getID(), true, true, $row['passwd'], $row['email'], true, $row['id'], $row['last_seen']);
								$user->setGroup(TBGSettings::get('defaultgroup'));
							}
							elseif ($row['activated'] == 0 && TBGContext::getRequest()->getParameter('import_inactive_users'))
							{
								$user = TBGUser::createNew($row['uname'], $row['realname'], $row['realname'], TBGContext::getScope()->getID(), false, false, $row['passwd'], $row['email'], true, $row['id']);
								$user->setGroup(TBGSettings::get('defaultgroup'));
							}
						}
						echo __('done') . '<br>';
					}
					echo __('Importing status data') . ' ... ';
					flush();
					B2DB::closeDBLink();
					B2DB::setHost($b1_hostname);
					B2DB::setDBname($b1_database);
					B2DB::setUname($b1_username);
					B2DB::setPasswd($b1_password);
					B2DB::setDBtype('mysql');
					B2DB::doConnect();
					$sql = 'select * from statustable';
					$res = B2DB::simpleQuery($sql);
					$sql2 = 'select * from severity';
					$res2 = B2DB::simpleQuery($sql2);
					$sql3 = 'select * from priority';
					$res3 = B2DB::simpleQuery($sql3);
					B2DB::closeDBLink();
					B2DB::setHost($orig_hostname);
					B2DB::setDBname($orig_database);
					B2DB::setUname($orig_username);
					B2DB::setPasswd($orig_password);
					B2DB::setDBtype($orig_dbtype);
					B2DB::doConnect();
					while ($res && $row = $res->fetch_array())
					{
						if (isset($row['id']))
						{
							B2DB::getTable('TBGListTypesTable')->doDeleteById($row['id']);
							$crit = new B2DBCriteria();
							$crit->addInsert(TBGListTypesTable::ID, $row['id']);
							$crit->addInsert(TBGListTypesTable::ITEMTYPE, TBGDatatype::STATUS);
							$crit->addInsert(TBGListTypesTable::ITEMDATA, $row['status_color']);
							$crit->addInsert(TBGListTypesTable::NAME, $row['status_desc']);
							B2DB::getTable('TBGListTypesTable')->doInsert($crit);
						}
					}
					while ($res2 && $row = $res2->fetch_array())
					{
						if (isset($row['id']))
						{
							B2DB::getTable('TBGListTypesTable')->doDeleteById($row['id']);
							$crit = new B2DBCriteria();
							$crit->addInsert(TBGListTypesTable::ID, $row['id']);
							$crit->addInsert(TBGListTypesTable::ITEMTYPE, TBGDatatype::SEVERITY);
							$crit->addInsert(TBGListTypesTable::ITEMDATA, $row['sev_no']);
							$crit->addInsert(TBGListTypesTable::NAME, $row['sev_desc']);
							B2DB::getTable('TBGListTypesTable')->doInsert($crit);
						}
					}
					while ($res3 && $row = $res3->fetch_array())
					{
						if (isset($row['id']))
						{
							B2DB::getTable('TBGListTypesTable')->doDeleteById($row['id']);
							$crit = new B2DBCriteria();
							$crit->addInsert(TBGListTypesTable::ID, $row['id']);
							$crit->addInsert(TBGListTypesTable::ITEMTYPE, TBGDatatype::PRIORITY);
							$crit->addInsert(TBGListTypesTable::ITEMDATA, $row['prio_no']);
							$crit->addInsert(TBGListTypesTable::NAME, $row['prio_desc']);
							B2DB::getTable('TBGListTypesTable')->doInsert($crit);
						}
					}
					echo __('done') . '<br>';
					if (TBGContext::getRequest()->getParameter('projects'))
					{
						try
						{
							foreach (TBGContext::getRequest()->getParameter('projects') as $p_id => $import_data)
							{
								B2DB::closeDBLink();
								B2DB::setHost($b1_hostname);
								B2DB::setDBname($b1_database);
								B2DB::setUname($b1_username);
								B2DB::setPasswd($b1_password);
								B2DB::setDBtype('mysql');
								B2DB::doConnect();
								if (isset($import_data['import_project']))
								{
									$project = array();
									$sql = 'select * from paks where id = ' . $p_id;
									$res = B2DB::simpleQuery($sql);
									while ($res && $row = $res->fetch_array())
									{
										$project['project'] = $row;
										echo __('Importing data from %project_name%', array('%project_name%' => '<b>' . $row['pak_desc'] . '</b>')) . '<br>';
										flush();
									}
									$sql = 'select * from editions where pak = ' . $p_id;
									$res = B2DB::simpleQuery($sql);
									while ($res && $row = $res->fetch_array())
									{
										$project['editions'][$row['id']]['edition'] = $row;
									}
									if (isset($project['editions']))
									{
										foreach ($project['editions'] as $e_id => $e_data)
										{
											$sql = 'select * from builds where edition = ' . $e_id;
											$res = B2DB::simpleQuery($sql);
											while ($res &&  $row = $res->fetch_array())
											{
												$project['editions'][$e_id]['builds'][$row['id']]['build'] = $row;
											}
										}
										if (isset($project['editions'][$e_id]['builds']))
										{
											foreach ($project['editions'][$e_id]['builds'] as $b_id => $b_data)
											{
												$sql = 'select * from affectstable where build_no = ' . $b_id . ' and edition = ' . $e_id;
												$res = B2DB::simpleQuery($sql);
												while ($res && $row = $res->fetch_array())
												{
													$project['editions'][$e_id]['builds'][$b_id]['issueaffects'][$row['bug_id']][] = $row;
												}
												if (isset($import_data['import_tbg_']))
												{
													$sql = 'select * from tbg_table where pak = ' . $p_id;
													$res = B2DB::simpleQuery($sql);
													if (isset($import_data['import_comments']))
													{
														$sql2 = 'select commentstable.* from commentstable join tbg_table on tbg_table.id = commentstable.bug_id where tbg_table.pak = ' . $p_id;
														$res2 = B2DB::simpleQuery($sql2);
													}
													echo __('Importing project issues') . ' ... ';
													flush();
													B2DB::closeDBLink();
													B2DB::setHost($orig_hostname);
													B2DB::setDBname($orig_database);
													B2DB::setUname($orig_username);
													B2DB::setPasswd($orig_password);
													B2DB::setDBtype($orig_dbtype);
													B2DB::doConnect();
													while ($res && $row = $res->fetch_array())
													{
														$bug_id = $row['id'];
														B2DB::getTable('TBGIssuesTable')->doDeleteById($bug_id);
														$crit = new B2DBCriteria();
														$crit->addInsert(TBGIssuesTable::ID, $bug_id);
														$crit->addInsert(TBGIssuesTable::ISSUE_NO, $bug_id);
														$crit->addInsert(TBGIssuesTable::POSTED, $row['posted']);
														$crit->addInsert(TBGIssuesTable::LAST_UPDATED, $row['last_update']);
														$crit->addInsert(TBGIssuesTable::TITLE, $row['title']);
														$crit->addInsert(TBGIssuesTable::PROJECT_ID, $p_id);
														$crit->addInsert(TBGIssuesTable::LONG_DESCRIPTION, $row['long_desc']);
														$crit->addInsert(TBGIssuesTable::REPRODUCABILITY, $row['repro_steps']);
														$crit->addInsert(TBGIssuesTable::ISSUE_TYPE, 1);
														$crit->addInsert(TBGIssuesTable::POSTED_BY, $row['uname']);
														$crit->addInsert(TBGIssuesTable::STATUS, $row['status']);
														$crit->addInsert(TBGIssuesTable::CATEGORY, 0);
														$crit->addInsert(TBGIssuesTable::STATE, (($row['closed'] == 1) ? TBGIssue::STATE_CLOSED : TBGIssue::STATE_OPEN));
														$crit->addInsert(TBGIssuesTable::SEVERITY, $row['severity']);
														$crit->addInsert(TBGIssuesTable::SCOPE, TBGContext::getScope()->getID());
														B2DB::getTable('TBGIssuesTable')->doInsert($crit);
														if (isset($import_data['import_editions']))
														{
															foreach ($project['editions'][$e_id]['builds'][$b_id]['issueaffects'][$bug_id] as $issue_affects)
															{
																$crit = new B2DBCriteria();
																if ($issue_affects['build_no'] != 0)
																{
																	$crit->addInsert(TBGIssueAffectsBuildTable::BUILD, $issue_affects['build_no']);
																	$crit->addInsert(TBGIssueAffectsBuildTable::ISSUE, $bug_id);
																	$crit->addInsert(TBGIssueAffectsBuildTable::CONFIRMED, $issue_affects['confirmed']);
																	$crit->addInsert(TBGIssueAffectsBuildTable::SCOPE, TBGContext::getScope()->getID());
																	B2DB::getTable('TBGIssueAffectsBuildTable')->doInsert($crit);
																}
																else
																{
																	$crit->addInsert(TBGIssueAffectsEditionTable::EDITION, $issue_affects['edition']);
																	$crit->addInsert(TBGIssueAffectsEditionTable::ISSUE, $bug_id);
																	$crit->addInsert(TBGIssueAffectsEditionTable::CONFIRMED, $issue_affects['confirmed']);
																	$crit->addInsert(TBGIssueAffectsEditionTable::SCOPE, TBGContext::getScope()->getID());
																	B2DB::getTable('TBGIssueAffectsEditionTable')->doInsert($crit);
																}
															}
														}
													}
													if (isset($import_data['import_comments']))
													{
														while ($res2 && $row = $res2->fetch_array())
														{
															$bug_id = $row['bug_id'];
															if (is_numeric($row['uname']))
															{
																TBGComment::createNew($row['title'], $row['comment'], $row['uname'], $bug_id, 1, 'core', (((int) $row['nda_comment'] == 1) ? 0 : 1), 0, false);
															}
														}
													}
													echo __('done') . '<br>';
													flush();
													B2DB::closeDBLink();
													B2DB::setHost($b1_hostname);
													B2DB::setDBname($b1_database);
													B2DB::setUname($b1_username);
													B2DB::setPasswd($b1_password);
													B2DB::setDBtype('mysql');
													B2DB::doConnect();
												}
											}
										}
									}
									B2DB::closeDBLink();
									B2DB::setHost($orig_hostname);
									B2DB::setDBname($orig_database);
									B2DB::setUname($orig_username);
									B2DB::setPasswd($orig_password);
									B2DB::setDBtype($orig_dbtype);
									B2DB::doConnect();
									echo __('Importing the project') . ' ... ';
									flush();
									B2DB::getTable('TBGProjectsTable')->doDeleteById($p_id);
									TBGProject::createNew($project['project']['pak_desc'], $p_id);
									echo __('done') . '<br>';
									if (isset($import_data['import_editions']))
									{
										echo __('Importing project editions and builds') . ' ... ';
										flush();
										foreach ($project['editions'] as $e_id => $e_data)
										{
											B2DB::getTable('TBGEditionsTable')->doDeleteById($e_id);
											TBGEdition::createNew($e_data['edition']['ed_desc'], $p_id, $e_id);
											foreach ($e_data['builds'] as $b_id => $b_data)
											{
												B2DB::getTable('TBGBuildsTable')->doDeleteById($b_id);
												$b_major = $b_data['build']['build_no'];
												$b_minor = 0;
												$b_rev = 0;
												if (strpos($b_data['build']['build_no'], '.'))
												{
													list ($b_major, $b_minor, $b_rev) = explode('.', $b_data['build']['build_no']);
												}
												TBGBuild::createNew($b_data['build']['build_desc'], $e_id, $b_major, $b_minor, $b_rev, $b_id);
											}
										}
										echo __('done') . '<br>';
									}
								}
								echo '<br>';
							}
						}
						catch (Exception $e)
						{
							echo '<b>' . __('An error occured when importing data from BUGS 1.9: %error%', array('%error%' => '</b><br><i>' . $e->getMessage() . '</i>'));
							echo '<br><a href="#" onclick="history.go(-1);"><b>&lt;&lt; ' . __('Back') . '</b></a>';
						}
					}
					else
					{
						echo '<b>' . __('There were no projects to import data from') . '</b>';
					}
					
					?><br>
					<b style="font-size: 13px;"><?php echo __('The import has been completed') ?>!</b>
					</div>
					<?php
					break;
			}
		}
		else
		{
			?>
			<table style="width: 100%" cellpadding=0 cellspacing=0>
				<tr>
				<td style="padding-right: 10px;">
					<table class="configstrip" cellpadding=0 cellspacing=0>
						<tr>
							<td class="cleft"><b><?php echo __('Import data from version 1.9'); ?></b></td>
							<td class="cright">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=2 class="cdesc">
							<?php echo __('To start importing data from your BUGS 1.9 installation, please enter the connection details below.'); ?>
							<br>
							<?php echo __('After pressing "Next", you will be presented with several options related to the import. More information is available in the %tbg_online_help%', array('%tbg_online_help%' => tbg_helpBrowserHelper('generalsettings', __('The Bug Genie online help')))); ?>.
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			<form accept-charset="<?php echo $charset; ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
			<input type="hidden" name="module" value="core">
			<input type="hidden" name="section" value="16">
			<input type="hidden" name="step" value="1">
			<div style="width: 500px; margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em;"><?php echo __('Connection settings'); ?></div>
			<table style="width: 500px;" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 150px; padding: 5px;"><b><?php echo __('Hostname'); ?></b></td>
					<td style="width: auto;"><input type="text" name="b1_hostname" value="<?php echo (TBGContext::getRequest()->getParameter('b1_hostname')) ? TBGContext::getRequest()->getParameter('b1_hostname') : ''; ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				</tr>
				<tr>
					<td style="padding: 5px;"><b><?php echo __('Username'); ?></b></td>
					<td><input type="text" name="b1_username" value="<?php echo (TBGContext::getRequest()->getParameter('b1_username')) ? TBGContext::getRequest()->getParameter('b1_username') : ''; ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				</tr>
				<tr>
					<td style="padding: 5px;"><b><?php echo __('Password'); ?></b></td>
					<td><input type="password" name="b1_password" value="<?php echo (TBGContext::getRequest()->getParameter('b1_password')) ? TBGContext::getRequest()->getParameter('b1_password') : ''; ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				</tr>
				<tr>
					<td style="padding: 5px;"><b><?php echo __('Database name'); ?></b></td>
					<td><input type="text" name="b1_database" value="<?php echo (TBGContext::getRequest()->getParameter('b1_database')) ? TBGContext::getRequest()->getParameter('b1_database') : 'tbg_db'; ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				</tr>
			<?php 
			
			if ($access_level == 'full')
			{
				?>
				<tr>
					<td colspan=3 style="padding: 5px; text-align: right;"><input type="submit" value="<?php echo __('Next'); ?> &gt;&gt;"></td>
				</tr>
				<?php 
			}

			?>
			</table>
			</form>
			<?php
		}
	}

?>