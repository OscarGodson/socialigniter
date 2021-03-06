<?php
class Settings extends Dashboard_Controller
{ 
    function __construct() 
    {
        parent::__construct();
        
        $this->data['page_title'] = 'Settings';
    } 
 
 	function index()
 	{
 		redirect('settings/profile');
 	}

	/* User Settings */
	function profile()
 	{	    
		$user = $this->social_auth->get_user('user_id', $this->session->userdata('user_id')); 

	    $this->data['sub_title'] 	= "Profile";     
 	 	$this->data['image']		= $user->image;
	 	$this->data['thumbnail']	= $this->social_igniter->profile_image($user->user_id, $user->image, $user->gravatar, 'small');
		$this->data['name']			= $user->name;
		$this->data['username']     = $user->username;			    
		$this->data['email']      	= $user->email;
		$this->data['language']		= $user->language;
		$this->data['time_zone']	= $user->time_zone;
		$this->data['geo_enabled']	= $user->geo_enabled;
		$this->data['privacy'] 		= $user->privacy;
        
		$this->render('dashboard_wide');
 	}
 	
 	function details()
 	{
		$user		= $this->social_auth->get_user('user_id', $this->session->userdata('user_id'));
		$user_meta	= $this->social_auth->get_user_meta($this->session->userdata('user_id'));

		foreach (config_item('user_meta_details') as $config_meta)
		{
			$this->data[$config_meta] = $this->social_auth->find_user_meta_value($config_meta, $user_meta);
		}

 	    $this->data['sub_title'] = "Details";
	 	
 		$this->render('dashboard_wide');	
 	}
 	
	function password() 
	{	
	    $this->data['sub_title']			= "Password";			 
    	$this->data['old_password']   		= $this->input->post('old_password');
    	$this->data['new_password']   		= $this->input->post('new_password');
    	$this->data['new_password_confirm'] = $this->input->post('new_password_confirm');
        
		$this->render('dashboard_wide');
	}

  	function mobile()
 	{
 	   	$user 		= $this->social_auth->get_user('user_id', $this->session->userdata('user_id')); 	
 		$user_meta	= $this->social_auth->get_user_meta_meta($this->session->userdata('user_id'), 'phone');
 	
 	    $this->data['sub_title'] 	= "Mobile";
 		$this->data['phones']		= $user_meta;
    	
 		$this->render('dashboard_wide');	
 	}
 	
  	function connections()
 	{
 	    $this->data['sub_title'] 			= "Connections";
		$this->data['social_connections']	= $this->social_igniter->get_settings_setting_value('social_connection', 'TRUE');
		$this->data['user_connections']		= $this->social_auth->get_connections_user($this->session->userdata('user_id'));
	    $this->data['message'] 				= validation_errors();

 		$this->render('dashboard_wide');	
 	}
 	
	function advanced() 
	{	
	    $this->data['sub_title']			= "Advanced";			 

        
		$this->render('dashboard_wide');
	} 	
 	
	/* Site Settings */
	function site()
	{
		$this->data['sub_title'] 	= 'Site';
		$this->data['this_module']	= 'site';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		
		$this->render('dashboard_wide');	
	}

	function themes()
	{
		$this->data['site']					= $this->social_igniter->get_site();
		$this->data['site_themes']			= $this->social_igniter->get_themes('site');
		$this->data['dashboard_themes']		= $this->social_igniter->get_themes('dashboard');
		$this->data['mobile_themes']		= $this->social_igniter->get_themes('mobile');
		$this->data['sub_title'] 			= 'Themes';
		$this->data['this_module']			= 'themes';
		$this->data['shared_ajax'] 		   .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		
		$this->render('dashboard_wide');
	}
	
	function design()
	{
		$this->data['sub_title'] 			= 'Design';
		$this->data['this_module']			= 'design';
		$this->data['shared_ajax'] 		   .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);
		$this->render('dashboard_wide');
	}

	function widgets()
	{
		$this->data['sub_title'] 		= 'Widgets';
		$this->data['this_module']		= 'widgets';
		$this->data['layouts']			= $this->social_igniter->scan_layouts(config_item('site_theme'));
		$this->data['layout_selected']	= 'site';	
		$this->data['shared_ajax'] 	   .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		

		// Build Widget Arrays
		// Will have to redo for different layouts
		$content_widgets	= array();		
		$sidebar_widgets	= array();
		$wide_widgets		= array();		
				
		foreach ($this->site_widgets as $site_widget)
		{
			if ($site_widget->setting == 'content')
			{
				$content_widgets[] = $site_widget;
			}
			elseif ($site_widget->setting == 'sidebar')
			{
				$sidebar_widgets[] = $site_widget;			
			}
			elseif ($site_widget->setting == 'wide')
			{
				$wide_widgets[] = $site_widget;			
			}
		}

		$this->data['content_widgets']	= $this->social_igniter->make_widgets_order($content_widgets);
		$this->data['sidebar_widgets']  = $this->social_igniter->make_widgets_order($sidebar_widgets);
		$this->data['wide_widgets']		= $this->social_igniter->make_widgets_order($wide_widgets);

		$this->render('dashboard_wide');
	}

	function services()
	{
		$mobile_modules = $this->social_igniter->get_settings_setting('is_mobile_module');
	
		$this->data['mobile_modules']['none'] = '--none--';
		
		foreach ($mobile_modules as $mobile_module)
		{
			$this->data['mobile_modules'][$mobile_module->module] = ucwords($mobile_module->module);
		}
		
		$this->data['sub_title'] 	= 'Services';
		$this->data['this_module']	= 'services';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		
		$this->render('dashboard_wide');	
	}
	
	function comments()
	{	
		$this->data['sub_title'] 	= 'Comments';
		$this->data['this_module']	= 'comments';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		
    	$this->render('dashboard_wide');
    }	

	function home()
	{
		$this->data['sub_title'] 	= 'Home';
		$this->data['this_module']	= 'home';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);
    	$this->render('dashboard_wide');
    }

	function users()
	{	
		$this->data['sub_title']	= 'Users';
		$this->data['this_module']	= 'users';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);
    	$this->render('dashboard_wide');
    }

	function places()
	{	
		$this->data['sub_title'] 	= 'Places';
		$this->data['this_module']	= 'places';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);
    	$this->render('dashboard_wide');
    } 
	
	function pages()
	{	
		$this->data['sub_title'] 	= 'Pages';
		$this->data['this_module']	= 'pages';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);
    	$this->render('dashboard_wide');
    }    

	function api()
	{
		$this->data['sub_title'] 	= 'API';
		$this->data['this_module']	= 'users';
		$this->data['shared_ajax'] .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);		
    	$this->render('dashboard_wide');
    }

    /* Modules Settings */
	function apps()
	{
		$this->data['sub_title']		= 'Apps';
		$this->data['core_modules']		= config_item('core_modules');
		$this->data['ignore_modules']	= config_item('ignore_modules');
		$this->data['modules']			= $this->social_igniter->scan_modules();
		$this->data['shared_ajax'] 	   .= $this->load->view(config_item('dashboard_theme').'/partials/settings_modules_ajax.php', $this->data, true);
		$this->render('dashboard_wide');
	}
	
	function get_apps()
	{
		$this->data['sub_title']		= 'Get Apps';	
		$this->render('dashboard_wide');
	}
}