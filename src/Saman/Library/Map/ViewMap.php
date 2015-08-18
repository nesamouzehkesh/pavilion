<?php

namespace Saman\Library\Map;

class ViewMap {
    const CONFIG_ADMIN_CONFIG_ITEM_ADD_EDIT = 'SamanConfigBundle:Config:index.html.twig';

    const APPEARANCE_ADMIN_NAVIGATIONS_HOME = 'SamanAppearanceBundle:Navigation:index.html.twig';
    const APPEARANCE_ADMIN_NAVIGATION_ADD = 'SamanAppearanceBundle:Navigation:form/addNavigation.html.twig';
    const APPEARANCE_ADMIN_NAVIGATION_EDIT = 'SamanAppearanceBundle:Navigation:element/editNavigation.html.twig';
    const APPEARANCE_ADMIN_NAVIGATIONS_VIEW = 'SamanAppearanceBundle:Navigation:element/navigations.html.twig';
    const APPEARANCE_ADMIN_NAVIGATION_VIEW = 'SamanAppearanceBundle:Navigation:element/navigation.html.twig';
    const APPEARANCE_ADMIN_LINK_ADD_EDIT = 'SamanAppearanceBundle:Link:form/addEditLink.html.twig';
    const APPEARANCE_ADMIN_LINKS_VIEW = 'SamanAppearanceBundle:Link:element/links.html.twig';
    
    const APPEARANCE_ADMIN_THEMES_HOME = 'SamanAppearanceBundle:Theme:index.html.twig';
    const APPEARANCE_ADMIN_THEME_ADD = 'SamanAppearanceBundle:Theme:form/addTheme.html.twig';
    const APPEARANCE_ADMIN_THEME_EDIT = 'SamanAppearanceBundle:Theme:element/editTheme.html.twig';
    const APPEARANCE_ADMIN_THEMES_VIEW = 'SamanAppearanceBundle:Theme:element/themes.html.twig';
    const APPEARANCE_ADMIN_THEME_VIEW = 'SamanAppearanceBundle:Theme:element/theme.html.twig';
    const APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_VIEW = 'SamanAppearanceBundle:Theme:element/_rowStructure.html.twig';
    const APPEARANCE_ADMIN_THEME_EDIT_ROW = 'SamanAppearanceBundle:Theme:form/editRow.html.twig';
    const APPEARANCE_ADMIN_THEME_ROW_STRUCTURE_WEB_VIEW = 'SamanAppearanceBundle:Theme:web/_rowWebStructure.html.twig';
    
    const APPEARANCE_ADMIN_WIDGET_ADD = 'SamanAppearanceBundle:Widget:form/addWidget.html.twig';
    const APPEARANCE_ADMIN_WIDGET_EDIT = 'SamanAppearanceBundle:Widget:form/editWidget.html.twig';
    
    const APPEARANCE_ADMIN_WIDGET_ICON_WEB_VIEW = 'SamanAppearanceBundle:Widget:web/_iconWidget.html.twig';
    const APPEARANCE_ADMIN_WIDGET_CAROUSEL_WEB_VIEW = 'SamanAppearanceBundle:Widget:web/_carouselWidget.html.twig';
    const APPEARANCE_ADMIN_WIDGET_MENU_WEB_BOOTSTRAP_VIEW = 'SamanAppearanceBundle:Widget:web/_menuBootstrapWidget.html.twig';
    const APPEARANCE_ADMIN_WIDGET_MENU_WEB_SIMPLE_VIEW = 'SamanAppearanceBundle:Widget:web/_menuSimpleWidget.html.twig';

    const SHOPPING_ADMIN_PAGE_ITEMS_HOME = 'SamanShoppingBundle:ShoppingAdmin:index.html.twig';
    const SHOPPING_ADMIN_PAGE_ITEM_ADD_EDIT = 'SamanShoppingBundle:ShoppingAdmin:form/addEditProduct.html.twig';
    const SHOPPING_ADMIN_PAGE_ITEMS_VIEW = 'SamanShoppingBundle:ShoppingAdmin:element/products.html.twig';
    const SHOPPING_ADMIN_PAGE_ITEM_VIEW = 'SamanShoppingBundle:ShoppingAdmin:element/product.html.twig';
    
    const CMS_ADMIN_PAGE_ITEMS_HOME = 'SamanCmsBundle:CmsAdmin:index.html.twig';
    const CMS_ADMIN_PAGE_ITEM_ADD_EDIT = 'SamanCmsBundle:CmsAdmin:form/addEditPage.html.twig';
    const CMS_ADMIN_PAGE_ITEMS_VIEW = 'SamanCmsBundle:CmsAdmin:element/pages.html.twig';
    const CMS_ADMIN_PAGE_ITEM_VIEW = 'SamanCmsBundle:CmsAdmin:element/page.html.twig';
    
    const CMS_INDEX = 'SamanCmsBundle:Cms:index.html.twig';
}