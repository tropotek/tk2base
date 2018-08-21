/**
 * main.js
 */

jQuery(function ($) {

  // Side menu
  $('.tk-ui-menu.nav-side').each(function () {
    $(this).addClass('nav').find('ul').addClass('nav');
    $(this).find('.submenu > a span').after('<span class="fa arrow"></span>');
    $(this).find('ul').addClass('nav-second-level');
    $(this).find('ul ul').addClass('nav-third-level');
    $(this).find('ul ul ul').addClass('nav-forth-level');
    $(this).find('.fa').addClass('fa-fw');
    $(this).metisMenu();
  });

  // dropdown menu
  $('.tk-ui-menu.nav-dropdown').each(function () {
    $(this).addClass('dropdown-menu dropdown-user');
  });

  //project_core.initDualListBox();
  project_core.initTinymce();
  project_core.initCodemirror();
  project_core.initMasqueradeConfirm();
  project_core.initTableDeleteConfirm();


});
