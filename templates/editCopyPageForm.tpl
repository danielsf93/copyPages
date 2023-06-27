{**
 * templates/editCopyPageForm.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Form for editing a copy page
 *}
<script src="{$pluginJavaScriptURL}/CopyPageFormHandler.js"></script>
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#copyPageForm').pkpHandler(
			'$.pkp.controllers.form.copyPages.CopyPageFormHandler',
			{ldelim}
				previewUrl: {url|json_encode router=$smarty.const.ROUTE_PAGE page="pages" op="preview"}
			{rdelim}
		);
	{rdelim});
</script>

{capture assign=actionUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.copyPages.controllers.grid.CopyPageGridHandler" op="updateCopyPage" existingPageName=$blockName escape=false}{/capture}
<form class="pkp_form" id="copyPageForm" method="post" action="{$actionUrl}">
	{csrf}
	{if $copyPageId}
		<input type="hidden" name="copyPageId" value="{$copyPageId|escape}" />
	{/if}
	{fbvFormArea id="copyPagesFormArea" class="border"}
		{fbvFormSection}
			{fbvElement type="text" label="plugins.generic.copyPages.path" id="path" value=$path maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.generic.copyPages.pageTitle" id="title" value=$title maxlength="255" inline=true multilingual=true size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
		{fbvFormSection}
			{capture assign="exampleUrl"}{url|replace:"REPLACEME":"%PATH%" router=$smarty.const.ROUTE_PAGE context=$currentContext->getPath() page="REPLACEME"}{/capture}
			{translate key="plugins.generic.copyPages.viewInstructions" pagesPath=$exampleUrl}
		{/fbvFormSection}
		{fbvFormSection label="plugins.generic.copyPages.content" for="content"}
			{fbvElement type="textarea" multilingual=true name="content" id="content" value=$content rich=true height=$fbvStyles.height.TALL variables=$allowedVariables}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormSection class="formButtons"}
		{fbvElement type="button" class="pkp_helpers_align_left" id="previewButton" label="common.preview"}
		{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
		{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
	{/fbvFormSection}
</form>
