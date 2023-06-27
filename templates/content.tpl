{**
 * templates/content.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Display Copy Page content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<div class="page">
	<h2>{$title|escape}</h2>
	{$content}
</div>

<hr>
<b>Mostar conteúdo de copyright aqui</b>

{include file="frontend/components/footer.tpl"}
