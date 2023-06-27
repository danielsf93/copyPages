{**
 * templates/copyPages.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Copy pages plugin -- displays the CopyPagesGrid.
 *}
{capture assign=copyPageGridUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.copyPages.controllers.grid.CopyPageGridHandler" op="fetchGrid" escape=false}{/capture}
{load_url_in_div id="copyPageGridContainer" url=$copyPageGridUrl}
