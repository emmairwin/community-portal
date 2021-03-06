<?php
    global $wpdb;
    $countries = em_get_countries();
    $em_countries = $wpdb->get_results("SELECT DISTINCT location_country FROM ".EM_LOCATIONS_TABLE." WHERE location_country IS NOT NULL AND location_country != '' AND location_status=1 ORDER BY location_country ASC", ARRAY_N);
    $ddm_countries = array();

    foreach($em_countries as $em_country) {
        $ddm_countries[$em_country[0]] = $countries[$em_country[0]];
    }

    asort($ddm_countries);
    
    $categories = EM_Categories::get();
    if(count($categories) > 0) {
        foreach($categories as $category) {
            $categories[$category->id] = $category->name;
        }
    } else {
        $categories = array(
            'Localization (L10N)',
            'User Support (SUMO)',
            'Testing',
            'Common Voice',
            'Coding',
            'Design',
            'Advocacy',
            'Documentation',
            'Evangelism',
            'Marketing',
        );
    }
?>
<div class="col-md-12 events__filter">
    <p class="events__filter__title">Filter By:</p>
    <form action="" class="events__filter__form">
        <?php
            $field_name = "Country";
            $field_label = __("Location", 'community-portal');
            $options = $ddm_countries;
            include(locate_template('plugins/events-manager/templates/template-parts/options.php', false, false));    

            $field_name =  "Tag";
            $field_label = __("Tag", 'community-portal');
            $options = $categories;
            include(locate_template('plugins/events-manager/templates/template-parts/options.php', false, false));   
            
            
            $field_name = "Initiative";
            $field_label = __("Campaign or Activity", 'community-portal');
            $args = Array(
                'post_type' =>  'campaign',
                'per_page'  =>  -1
            );
        
            $campaigns = new WP_Query($args);
            $initiatives = Array();

            foreach($campaigns->posts AS $campaign) {
                $start = strtotime(get_field('campaign_start_date', $campaign->ID));
                $end = strtotime(get_field('campaign_end_date', $campaign->ID));
                $today = time();

                $campaign_status = get_field('campaign_status', $campaign->ID);

                if(strtolower($campaign_status) !== 'closed') {
                    $initiatives[$campaign->ID] = $campaign->post_title;
                    continue;
                }

                if($today >= $start && $today <= $end) {
                    $initiatives[$campaign->ID] = $campaign->post_title;
                }
            }

            $args = Array(
                'post_type' =>  'activity',
                'per_page'  =>  -1
            );

            $activities = new WP_Query($args);
            foreach($activities->posts AS $activity) {
                $initiatives[$activity->ID] = $activity->post_title;
            }

            $options = $initiatives;
            include(locate_template('plugins/events-manager/templates/template-parts/options.php', false, false));   

        ?>
    </form>
</div>
<div class="col-md-12">
    <button class="events__filter__toggle btn btn--large btn--light">
        <?php echo __('Show Filters') ?>
    </button>
</div>