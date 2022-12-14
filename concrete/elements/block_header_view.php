<?php
defined('C5_EXECUTE') or die("Access Denied.");
$app = \Core::make('app');
if ($a->isGlobalArea()) {
    $c = Page::getCurrentPage();
    $cID = $c->getCollectionID();
} else {
    $cID = $b->getBlockCollectionID();
    $c = $b->getBlockCollectionObject();
}

$p = new Permissions($b);
$showMenu = false;
if ($a->showControls() && $p->canViewEditInterface() && $view->showControls()) {
    $showMenu = true;
}

$css = $b->getCustomStyle();
$pt = $c->getCollectionThemeObject();

if ($showMenu) {
    ?>
    <div data-container="block">
<?php
} ?>

<?php if (is_object($css) && $b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <?php // in this instance, the css container comes OUTSIDE any theme container ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
<?php
} ?>

<?php
if (
    $pt->supportsGridFramework()
    && $a->isGridContainerEnabled()
    && !$b->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
    echo $gf->getPageThemeGridFrameworkContainerStartHTML();
    echo $gf->getPageThemeGridFrameworkRowStartHTML();
    printf('<div class="%s">', $gf->getPageThemeGridFrameworkColumnClassesForSpan(
        min($a->getAreaGridMaximumColumns(), $gf->getPageThemeGridFrameworkNumColumns())
    ));
}

if ($showMenu) {
    $arHandle = $a->getAreaHandle();

    $btw = BlockType::getByID($b->getBlockTypeID());
    if (in_array($btw->getBlockTypeHandle(), [BLOCK_HANDLE_LAYOUT_PROXY, BLOCK_HANDLE_CONTAINER_PROXY])) {
        $class = 'ccm-block-edit-layout ccm-block-edit';
    } else {
        $class = 'ccm-block-edit';
    }

    $class .= ($b->isAliasOfMasterCollection() || $b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) ? " ccm-block-alias" : "";

    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
        $class .= ' ccm-block-stack ';
    }
    $editInline = false;
    if ($btw->supportsInlineEdit()) {
        $editInline = true;
    }
    $aID = $a->getAreaID();
    $btHandle = $btw->getBlockTypeHandle();
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
        $_bi = $b->getInstance();
        $_bo = Block::getByID($_bi->getOriginalBlockID());
        $btOriginal = \Concrete\Core\Block\BlockType\BlockType::getByHandle($_bo->getBlockTypeHandle());
        $btHandle = $btOriginal->getBlockTypeHandle();
    }

    ?>

    <div
        data-cID="<?=$c->getCollectionID()?>"
        data-area-id="<?=$a->getAreaID()?>"
        data-block-id="<?=$b->getBlockID()?>"
        data-block-type-wraps="<?= intval(!$b->ignorePageThemeGridFrameworkContainer(), 10) ?>"
        class="<?=$class?>"
        data-block-type-handle="<?=$btHandle?>"
        data-launch-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"
        data-dragging-avatar="<?=h('<p><img src="' . Loader::helper('concrete/urls')->getBlockTypeIconURL($btw) . '" /><span>' . t($btw->getBlockTypeName()) . '</span></p>')?>"
        <?php if (in_array($btw->getBlockTypeHandle(), [BLOCK_HANDLE_LAYOUT_PROXY, BLOCK_HANDLE_CONTAINER_PROXY])) { ?>
    data-block-menu-handle="none"<?php
}
    ?>
        >

    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
    <?php
}
    ?>

        <ul class="<?php if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_CONTAINER_PROXY) { ?>ccm-edit-mode-inline-container<?php } ?> ccm-edit-mode-inline-commands ccm-ui">
            <?php if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_CONTAINER_PROXY) { ?>
            <li><span><?php
                $containerBlockController = $b->getController();
                if ($containerBlockController instanceof Concrete\Block\CoreContainer\Controller) {
                    $containerBlockContainerInstance = $containerBlockController->getContainerInstanceObject();
                    if ($containerBlockContainerInstance) {
                        $containerBlockContainer = $containerBlockContainerInstance->getContainer();
                        if ($containerBlockContainer) {
                            print $containerBlockContainer->getContainerDisplayName();
                        }
                    }
                } ?>
                </span></li>
                <?php
                $showContainerDesign = ($p->canEditBlockDesign() && Config::get('concrete.design.enable_custom') == true);
                if ($showContainerDesign) { ?>
                    <li><a data-container-block-id="<?= $b->getBlockID() ?>" data-inline-command="edit-container-design" href="#"><i class="fas fa-paint-brush"></i></a></li>
                <?php } ?>
            <?php } ?>
            <?php if ($p->canEditBlock() && (!in_array($btw->getBlockTypeHandle(), [BLOCK_HANDLE_LAYOUT_PROXY]))) { ?>
                <li><a class="ccm-edit-mode-inline-command-move" data-inline-command="move-block" href="#"><i class="fas fa-arrows-alt"></i></a></li>
            <?php } ?>
            <?php if ($p->canDeleteBlock() && $btw->getBlockTypeHandle() == BLOCK_HANDLE_CONTAINER_PROXY) { ?>
                <li><a class="ccm-edit-mode-inline-command-delete" data-inline-command="delete-block" href="#"><i class="fas fa-trash"></i></a></li>
            <?php } ?>
        </ul>

        <div class="ccm-ui">
            <?php
            $factory = $app->make('Concrete\Core\Block\Menu\Manager');
            $menu = $factory->getMenu([$b, $c, $a]);
            print $factory->deliverMenu($menu);
            ?>
        </div>

<?php
} else {
    ?>
    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
    <?php
}
    ?>
<?php
} ?>
