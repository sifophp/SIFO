<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
{$modules.head}
	<body>
		<div id="doc2" class="yui-t2">
{	$modules.header}
			<div id="bd" class="equalize" role="main">
{				$modules.system_messages}

				<div id="yui-main">
					<div class="yui-b">
						<div class="main-content">
							<h2>Example content</h2>

							<p>
								This example is using <a href="http://developer.yahoo.com/yui/grids/">YUI CSS Grids</a>
								{* and <a href="http://www.cssnewbie.com/equalheights-jquery-plugin/">JQuery equal column heights plugin</a>. Give it a try!*}
							</p>

							<p>{t 1=$mood}This is how i feel... %1{/t}</p>

							<div class="yui-gd">
								<div class="yui-u first">
									<form action="" method="POST">
										<fieldset>
											<legend>1. Try validation functions</legend>
											<input type="hidden" name="testform" value="true" />
											<p>Use valid/invalid email:<br />
												<input type="text" name="email" value="{$form_fields.email}" />
											</p>
											<p>Use valid/invalid phone:<br />
												<input type="text" name="phone" value="{$form_fields.phone}" />
											</p>
										</fieldset>
										<input type="submit" name="validate" value="Validate">
									</form>
								</div>
								<div class="yui-u">
									<form action="" method="POST">
										<fieldset>
											<legend>2. Try Open Inviter <small>(<a href="http://openinviter.com/">more info</a>)</small></legend>
											<input type="hidden" name="inviteform" value="true" />
											<p>Your email:<br />
												<input type="text" name="account_email" value="{$inviter_email}" />
											</p>
											<p>Your password:<br />
												<input type="password" name="account_password" />
											</p>
										</fieldset>
										<input type="submit" name="validate" value="Get contacts">
									</form>
{								if $friends}
									<ul style="max-height:300px;overflow-y:scroll;padding:9px;background:#fff;border:1px solid #ccc;margin:18px 0;">
{									foreach item=name key=email from=$friends}
										<li><strong>{$name}</strong> - {$email}</li>
{									/foreach}
									</ul>
{								/if}
								</div>
							</div>

							<div class="yui-gb">
								<div class="yui-u first">
									<h3>Column 1</h3>
									<p>Lorem Ipsum is simply dummy text of the printing and typesetting
										industry. Lorem Ipsum has been the industry's standard dummy text e
										ver since the 1500s, when an unknown printer took a galley of type
										and scrambled it to make a type specimen book. It has survived not
										only five centuries, but also the leap into electronic typesetting,
										remaining essentially unchanged. It was popularised in the 1960s
										with the release of Letraset sheets containing Lorem Ipsum passages,
										and more recently with desktop publishing software like Aldus
										PageMaker including versions of Lorem Ipsum.</p>
								</div>
								<div class="yui-u">
									<h3>Column 2</h3>
									<p>Contrary to popular belief, Lorem Ipsum is not simply random
										text. It has roots in a piece of classical Latin literature
										from 45 BC, making it over 2000 years old. Richard McClintock,
										a Latin professor at Hampden-Sydney College in Virginia,
										looked up one of the more obscure Latin words, consectetur,
										from a Lorem Ipsum passage, and going through the cites of the
										word in classical literature, discovered the undoubtable
										source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33
										of "de Finibus Bonorum et Malorum" (The Extremes of Good and
										Evil) by Cicero, written in 45 BC. This book is a treatise on
										the theory of ethics, very popular during the Renaissance. The
										first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes
										from a line in section 1.10.32.</p>
								</div>
								<div class="yui-u">
									<h3>Column 3</h3>
									<p>The standard chunk of Lorem Ipsum used since the 1500s
										is reproduced below for those interested. Sections 1.10.32
										and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero
										are also reproduced in their exact original form, accompanied
										by English versions from the 1914 translation by H. Rackham.</p>
								</div>
							</div>

{							pagination data=$pagination_data}
						</div>
					</div>

				</div>

				<div class="yui-b">
					<div class="sidebar">
						<ul>
							<li><a href="{$url.main}">Home</a></li>
							<li><a href="{$url.section_1}">Section 1</a></li>
							<li><a href="{$url.section_2}">Section 2</a></li>
							<li><a href="{$url.section_3}">Section 3</a></li>
						</ul>

						<h4>Example of banner via module</h4>
						{$modules.ads_google_skyscrapper}
					</div>
				</div>
			</div>
			<div id="ft" role="contentinfo">
				<div class="footer">
					SEO Framework - <small>sharing knowledge since 2009</small>
				</div>
			</div>
		</div>
	</body>
</html>
