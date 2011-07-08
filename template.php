<?php
/* debug*/
//drupal_theme_rebuild();


if (file_exists(libraries_get_path('modernizr') . '/js/modernizr.js'))
  drupal_add_js(libraries_get_path('modernizr') . '/js/modernizr.js', array('group' => JS_THEME, 'every_page' => TRUE));

//dependns: html5_tools, libraries

// hook_html_head_alter().
function ground_html_head_alter(&$head_elements) {
  //Place favicon.ico & apple-touch-icon.png in the root of your domain -->
  foreach ($head_elements as $name => $val)
    if ($val['#tag'] == 'link' && $val['#attributes']['rel'] == 'shortcut icon')
      unset($head_elements[$name]);
      
  unset($head_elements['system_meta_generator']);//TODO: оставить?

  $head_elements['viewport'] = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0')
  );
  
  $head_elements['chrome_frame'] = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array('http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge,chrome=1')
  );
}

function _ground_html_conditional($language, $rdf,$head_classes, $conditional_class) {
  return "<html lang=\"{$language->language}\" dir=\"$language->dir\" {$rdf->version} {$rdf->namespaces} class=\"$head_classes $conditional_class\">";
}

function ground_preprocess_html(&$vars) {
  if (module_exists('rdf')) {
    $vars['doctype'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML+RDFa 1.1//EN">' . "\n";
    $vars['rdf']->version = ' version="HTML+RDFa 1.1"';
    $vars['rdf']->namespaces = $vars['rdf_namespaces'];
    $vars['rdf']->profile = ' profile="' . $vars['grddl_profile'] . '"';
  } else {
    $vars['doctype'] = '<!DOCTYPE html>' . "\n";
    $vars['rdf']->version = '';
    $vars['rdf']->namespaces = '';
    $vars['rdf']->profile = '';
  }
  if (function_exists('locale')) {
    $vars['classes_array'][] = 'lang-'. $vars['language']->language;
  }
  $vars['head_classes_array'][] = 'no-js';
  
  // Add a CSS class based on the current page context.
  //FIXME: need or not ?
  if (!drupal_is_front_page()) {
    $context = explode('/', drupal_get_path_alias());
    $context = reset($context);
    if (!empty($context)) {
      $vars['attributes_array']['class'][] = drupal_html_class('context-' . $context);
    }
  }
  
}

function ground_process_html(&$vars) {
  $vars['head_classes'] = implode(' ', $vars['head_classes_array']);
}

function ground_process_html_tag(&$vars) {
  $el = &$vars['element'];
  // Remove media="all" but leave others unaffected.
  if (isset($el['#attributes']['media']) && $el['#attributes']['media'] === 'all') {
    unset($el['#attributes']['media']);
  }
}

function ground_preprocess(&$vars, $hook) {
  if (isset($vars['attributes_array']) && isset($vars['classes_array']))
    $vars['attributes_array']['class'] = &$vars['classes_array'];
  if (!empty($vars['classes_array']))
    foreach($vars['classes_array'] as $i => $class)
      if (substr($class, -1) == '-') unset($vars['classes_array'][$i]);
}

//TODO: optimize
//allow <name>_attributes_array 
function ground_process(&$vars) {
  foreach ($vars as $name => $val) {
    if (strpos($name, 'attributes_array') !== false) {
      $n = str_replace('_array', '', $name);
      if (!empty($val) && empty($vars[$n]) && is_array($val))
        $vars[$n] = drupal_attributes($val);
    }
  }
}

/**
В текущей вирсии drupal перезаписывается свойство #pre_render
http://drupal.org/node/914792
*/
function _ground_fix_pre_render(&$e) {
  if(empty($e['#type'])) return;
  $info = element_info($e['#type']);
  if (!empty($info['#pre_render']))
    $e['#pre_render'] = empty($e['#pre_render']) ? $info['#pre_render'] :
      array_merge($e['#pre_render'], $info['#pre_render']);
}

//to render subelements
function ground_element_render(&$elements) {
  $names = empty($elements['#element_render']) ? array('#value') : $elements['#element_render'];
  _ground_fix_pre_render($elements);
  foreach ((array) $names as $name) {
    _ground_fix_pre_render($elements[$name]);
    foreach (element_children($elements[$name]) as $ch)
      _ground_fix_pre_render($elements[$name][$ch]);
    $elements[$name] = render($elements[$name]);
  }
  return $elements;
}

function ground_theme() {
  $path = drupal_get_path('theme', 'ground') . '/templates';
  return array(
    'ground_wrapper' => array(
      'render element' => 'element',
    ),
    //split page.tpl.php
    'page_header' => array(
      'template' => 'page-header',
      'path' => $path,
      'variables' => array('page' => array())
    ),
    'page_menu' => array(
      'template' => 'page-menu',
      'path' => $path,
      'variables' => array('page' => array())
    ),
    'page_content' => array(
      'template' => 'page-content',
      'path' => $path,
      'variables' => array('page' => array())
    ),
  );
}

//FIXME: twice render -> twice wrap
function ground_ground_wrapper(&$vars) {
  $element = &$vars['element'];
  $default_wrapper = array('#type' => 'html_tag', '#tag' => 'div', '#wrapper_element' => '#value');
  $wrapper = empty($element['#wrapper']) ? $default_wrapper : array_merge($default_wrapper, $element['#wrapper']);
  $wrapper[$wrapper['#wrapper_element']] = $element['#children'];
  return drupal_render($wrapper);
}

function ground_preprocess_page(&$vars) {
  $vars['attributes_array']['id'] = 'page';
  $vars['primary_local_tasks'] = menu_primary_local_tasks();
  $vars['secondary_local_tasks'] = menu_secondary_local_tasks();
}

function ground_process_page(&$vars) {
  $vars['page_header'] = theme('page_header', array('page' => $vars));
  $vars['page_content'] = theme('page_content',  array('page' => $vars));
  $vars['page_menu'] = theme('page_menu',  array('page' => $vars));
}

function ground_preprocess_page_header(&$vars) {
  $page = $vars['page'];
  $vars['attributes_array']['role'] = 'banner';
  $vars['attributes_array']['class'][] = 'clearfix';
  $vars['logo'] = array(
    '#theme_wrappers' => array('ground_wrapper'),
    '#access' => !empty($page['logo']),
    '#wrapper' => array(
      '#type' => 'link',
      '#options' => array('html' => true, 'attributes' => array('rel' => 'home', 'title' => t('Home page'))),
      '#href' => '<front>',
      '#wrapper_element' => '#title'
    ),
    'logo_img' => array(
      '#prefix' => '<div id="logo">',
      '#suffix' => '</div>',
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => array('src' => check_url($page['logo']), 'alt' => check_plain(variable_get('site_name', '')) .' '. t('logo'))
      )
    );
  $vars['site_name'] = array(
    '#theme_wrappers' => array('ground_wrapper'),
    '#access' => !empty($page['site_name']),
    '#wrapper' => array('#tag' => 'h1', '#attributes' => array('id' => 'site-name')),
    'site_link' => array(
        '#type' => 'link',
        '#options' => array('html' => true, 'attributes' => array('rel' => 'home', 'title' => t('Home'))),
        '#href' => '<front>',
        '#title' => "<span>{$page['site_name']}</span>"
      )
  );
  $vars['site_slogan'] = array(
    '#type' => 'html_tag',
    '#tag' => 'h2',
    '#access' => !empty($page['site_slogan']),
    '#attributes' => array('id' => "site-slogan"),
    '#value' => $page['site_slogan']
  );
  $vars['group_attributes_array']['id'] = 'name-and-slogan';
}

function ground_preprocess_page_menu(&$vars) {
  $page = $vars['page'];
  $vars['attributes_array']['role'] = 'navigation';
  $vars['main_menu'] = array(
    '#theme' => 'links__system_main_menu',
    '#links' => &$page['main_menu'],
    '#access' => !empty($page['main_menu']),
    '#attributes' => array('id' => 'main-menu', 'class' => array('links', 'inline', 'clearfix')),
    '#heading' => array('text' => t('Main menu'), 'level' => 'h2', 'class' => array('element-invisible'))
  );
  $vars['secondary_menu'] = array(
      '#theme' => 'links__system_secondary_menu',
      '#links' => &$page['secondary_menu'],
      '#access' => !empty($page['secondary_menu']),
      '#attributes' => array('id' => 'secondary-menu', 'class' => array('links', 'inline', 'clearfix')),
      '#heading' => array('text' => t('Secondary menu'), 'level' => 'h2', 'class' => array('element-invisible'))
    );
}

function ground_preprocess_page_content(&$vars) {
  $page = $vars['page'];
  $title_classes = array();
  if (empty($page['title'])) {
    $page['title'] = t('Main content');
    $title_classes[] = 'element-invisible';
  }
  $vars['attributes_array'] = array('id' => 'main-wrapper', 'class' => 'clearfix');
  $vars['section_tag'] = $page['title'] ? 'section' : 'div';
  $vars['section_attributes_array']['id'] = 'main-content';
  $vars['content_attributes_array']['id'] = 'content';
  $vars['header_attributes_array']['class'] = 'content-header';
  $vars['footer_attributes_array']['class'] = 'content-footer';
  
  $vars['title'] = array(
    '#access' => !empty($page['title']),
    '#type' => 'html_tag', '#tag' => 'h1', '#value' => $page['title'],
    '#attributes' => array('id' => 'page-title', 'class' => $title_classes),
  );
  $vars['tasks'] = array(
    '#access' => !empty($page['primary_local_tasks']) || !empty($page['secondary_local_tasks']) || !empty($page['action_links']),
    '#theme_wrappers' => array('ground_wrapper'),
    '#wrapper' => array('#attributes' => array('id' => 'tasks')),
    'primary_tasks' => array(
      '#access' => !empty($page['primary_local_tasks']),
      '#theme_wrappers' => array('ground_wrapper'),
      '#wrapper' => array('#tag' => 'ul', '#attributes' => array('class' => 'tabs primary')),
      'tasks' => &$page['primary_local_tasks']
    ),
    'secondary_tasks' => array(
      '#access' => !empty($page['secondary_local_tasks']),
      '#theme_wrappers' => array('ground_wrapper'),
      '#wrapper' => array('#tag' => 'ul', '#attributes' => array('class' => 'tabs secondary')),
      'tasks' => &$page['secondary_local_tasks']
    ),
    'action_links' => array(
      '#access' => !empty($page['action_links']),
      '#theme_wrappers' => array('ground_wrapper'),
      '#wrapper' => array('#tag' => 'ul', '#attributes' => array('class' => 'action-links"')),
      'tasks' => &$page['action_links']
    ),
  );
}

function ground_preprocess_region(&$vars) {
  $list = system_region_list($GLOBALS['theme']);
  $vars['title'] = $list[$vars['region']];
  $vars['title_attributes_array']['class'][] = 'element-invisible';
  $vars['title_attributes_array']['class'][] = 'region-title';
  if (strpos($vars['region'], 'sidebar_') === 0)
    $vars['classes_array'][] = 'sidebar';
}

function ground_preprocess_block(&$vars) {
  static $nav_blocks = array('navigation', 'main-menu', 'management', 'user-menu', 'superfish', 'nice_menus');
  static $nav_modules = array('superfish', 'nice_menus');
  
  //Title
  $block = &$vars['block'];
  if (empty($block->subject)) {
    $block->subject = t("{$block->module} {$block->delta} block");
    $vars['title_attributes_array']['class'][] = 'element-invisible';
  }
  
  //Classes
  $vars['classes_array'][] = 'block-' . $vars['block_zebra'];
  $vars['classes_array'][] = 'block-count-'. $vars['id'];
  $vars['classes_array'][] = $region_class =  drupal_html_class('block-region-' . $vars['block']->region);
  $vars['classes_array'][] = $region_class . '-' . $block->module . '-' .  $block->delta;
  $vars['title_attributes_array']['class'][] = 'block-title';
  $vars['content_attributes_array']['class'][] = 'block-content';
  $vars['attributes_array']['id'] = $vars['block_html_id'];
  $vars['classes_array'][] = $vars['block_html_id'];
  
  //Suggestions
  $vars['is_menu_block'] = in_array($vars['block']->delta, $nav_blocks) ||
    in_array($vars['block']->module, $nav_modules);
  if ($vars['is_menu_block'])
    $vars['theme_hook_suggestions'][] = 'block__menu';
  // For example we can have templates such as block--header--search, or block--menu-bar--menu, which is cool...
  $vars['theme_hook_suggestions'][] = 'block__' . $vars['block']->region . '__' . $vars['block']->module;
  
  //section tag
  $vars['section_tag'] = 'section';
  switch (true) {
    case $block->module == 'search':
      $vars['section_tag'] = 'div';
      break;
    case $vars['is_menu_block']:
      $vars['section_tag'] = 'nav';
      break;
  }
}


function ground_preprocess_node(&$vars) {
  $node = $vars['node'];
  $vars['attributes_array']['id'] = drupal_html_id('node-' . $vars['type'] . '-' . $vars['nid']);
  $vars['classes'][] = 'clearfix';
  $vars['content_attributes_array']['class'] = array('content', 'clearfix');
  // Add a class to allow styling based on publish status.
  if ($vars['status']) {
    $vars['attributes_array']['class'][] = 'node-published';
  }
  // Add a class to allow styling based on promotion.
  if (!$vars['promote']) {
    $vars['attributes_array']['class'][] = 'node-not-promoted';
  }
  // Add a class to allow styling based on sticky status.
  if (!$vars['sticky']) {
    $vars['attributes_array']['class'][] = 'node-not-sticky';
  }
  // Add a class to allow styling of nodes being viewed by the author of the node in question.
  if ($vars['uid'] == $vars['user']->uid) {
    $vars['attributes_array']['class'][] = 'self-posted';
  }
  // Add a class to allow styling based on the node author.
  $vars['attributes_array']['class'][] = drupal_html_class('author-' . $vars['node']->name);
  // Add a class to allow styling for zebra striping.
  $vars['attributes_array']['class'][] = drupal_html_class($vars['zebra']);
  // Adding a class to the title attributes
  $vars['title_attributes_array']['class'] = 'node-title';
  
  $vars['classes_array'][] = 'node-lang-'. $vars['node']->language;
  
  if (!$vars['teaser']) {
    $vars['classes_array'][] = drupal_html_class('node-' . $vars['view_mode']);
  }
  $vars['content_attributes_array']['class'][] = 'node-content';
  $vars['footer_attributes_array']['class'][] = 'submitted';
  $vars['nav_attributes_array']['class'] = array('clearfix', 'node-links');
  
  $vars['render_title'] = array(
    '#theme_wrappers' => array('ground_wrapper'),
    '#wrapper' => array('#tag' => 'h1', '#attributes' => $vars['title_attributes_array']),
    '#access' => !empty($vars['title']) && !$vars['page'],
    'link' => array(
      '#type' => 'link',
      '#title' => $vars['title'],
      '#options' => array('attributes' => array('rel' => 'bookmark')),
      '#href' => trim($vars['node_url'], '/')
    )
  );
  $vars['header_attributes_array'] = array();
}

function ground_preprocess_comment_wrapper(&$vars) {
  $vars['attributes_array']['id'] = 'comments';
  $vars['title'] = array(
    '#type' => 'html_tag',
    '#tag' => 'h2',
    '#attributes' => array('class' => array('comments-title','title')),
    '#value' => t('Comments'),
    '#access' => true
  );
  $vars['form_title'] = array(
    '#type' => 'html_tag',
    '#tag' => 'h2',
    '#attributes' => array('class' => array('comments-form-title','title')),
    '#value' => t('Add new comment'),
  );
  $vars['header_attributes_array']['class'][] = 'comments-header';
  $vars['content_attributes_array']['class'][] = 'comments-content';
  $vars['footer_attributes_array']['class'][] = 'comment-form-wrapper';
}

function ground_preprocess_comment(&$vars) {
  $vars['signature_attributes_array']['class'][] = 'user-signature';
  $vars['links_attributes_array']['class'] = array('links comment-links', 'clearfix');
  
  $vars['attributes_array']['class'][] = 'clearfix';
  $vars['unpublished'] = '';
  if ($vars['status'] == 'comment-unpublished') {
    $vars['unpublished'] = '<div class="unpublished">' . t('Unpublished') . '</div>';
  }
  
  $vars['render_title'] = array(
    '#type' => 'html_tag',
    '#tag' => 'h3',
    '#attributes' => $vars['title_attributes_array'],
    '#access' => !empty($vars['title']),
    '#value' => $vars['title'],
  );
  $vars['content_attributes_array']['class'][] = 'comment-content';
  $vars['header_attributes_array']['class'][] = 'comment-header';
  $vars['footer_attributes_array']['class'][] = 'comment-footer';
}

function ground_preprocess_user_profile_category(&$vars) {
  $vars['classes_array'][] = 'user-profile-category-' . drupal_html_class($vars['title']);
  $vars['render_title'] = array(
    '#type' => 'html_tag',
    '#tag' => 'h3',
    '#attributes' => array('class' => array('user-profile-category-title')),
    '#value' => $vars['title'],
    '#access' => !empty($vars['title'])
  );
  $vars['content_attributes_array']['class'] = 'user-profile-category-content';
}

//TODO: views.view.tpl.php


