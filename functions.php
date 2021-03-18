<?php
function ffws_activation_success() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Fusion Builder FAQs with Google Schema Element added successfully.</p>
        </div>
        <?php
}

function ffws_element_init() {
    $input_tags = ['h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','paragraph'=>'Paragraph','span'=>'Span','div'=>'Div'];
    fusion_builder_map( array(
        'name'          => _( 'Google FAQs schema' ),
        'shortcode'     => 'ffws_faqs_with_schema',
        'preview'       =>  FFWS_PLUGIN_DIR.'preview/ffws_content_preview.php',
        'preview_id'    =>  'fusion-builder-ffws-content-preview',
        'icon'          => 'fusiona-exclamation-sign',
        'add_edit_items'=>_('Add / Edit Questions'),
        'sortable_items_info'=>_('Add or edit new question for this element.  Drag and drop them into the desired order.'),
        'params'        => array(
            array(
                'type'        => 'textarea',
                'heading'     => _( 'Question' ),
                'description' => 'Question for users & Google schema',
                'param_name'  => 'ffws_element_question'
            ),
            array(
                'type'        => 'tinymce',
                'heading'     => _( 'Answer' ),
                'description' => 'Answer to display for users.',
                'param_name'  => 'element_content'
            ),
            array(
                'type'        => 'textarea',
                'heading'     => _( 'Answer for Google schema' ),
                'param_name'  => 'element_schema_text',
                'description' => 'Add your answer for Google schema.'
            ),
            array(
                'type'        => 'select',
                'heading'     => _( 'Question Tag' ),
                'param_name'  => 'ffws_question_tag',
                'description' => 'Question HTML element tag for users',
                'value'       => $input_tags,
            ),
            array(
                'type'        => 'textfield',
                'heading'     => _( 'CSS Question Class' ),
                'param_name'  => 'ffws_question_class',
                'description' => 'Question tag Class attribute for users',
                'value'       => ''
            ),
            array(
                'type'        => 'textfield',
                'heading'     => _( 'CSS Question ID' ),
                'description' => 'Question tag ID attribute for users',
                'param_name'  => 'ffws_question_id'
            ),
            array(
                'type'        => 'upload',
                'heading'     => _( 'Google schema Image' ),
                'param_name'  => 'ffws_element_image',
                'value'       => '',
                'description' => 'Image for google schema'
            ),
            array(
                'type'        => 'textarea',
                'heading'     => _( 'Google schema reference' ),
                'param_name'  => 'ffws_element_sameas',
                'value'       => '',
                'description' => '`sameAs` reference. (comma saperated)'
            ),
            array(
                'type'        => 'checkbox_button_set',
                'heading'     => esc_attr__( 'Element Visibility', 'fusion-core' ),
                'param_name'  => 'ffws_hide_on_mobile',
                'value'       => fusion_builder_visibility_options( 'full' ),
                'default'     => fusion_builder_default_visibility( 'array' ),
                'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-core' ),
            )
        ),
    ));
}
add_action( 'fusion_builder_before_init', 'ffws_element_init', 11 );

function ffws_faqs_with_schema($args=[],$content=null) {
    $atts = shortcode_atts([
        'ffws_element_question'=>'',
        'ffws_element_answer'=>$content,
        'ffws_question_tag'=>'',
        'ffws_question_id'=>'',
        'ffws_question_class'=>'',
        'ffws_element_image'=>'',
        'element_schema_text'=>'',
        'ffws_hide_on_mobile'=>'',
        'ffws_element_sameas'=>''
    ],$args);

    $ffws_schema_data = get_transient('ffws_faqs_with_schema_data');
	
    if($ffws_schema_data){
        $ffws_schema_data[$atts['ffws_element_question']] = $atts;
    }else{
        $ffws_schema_data[$atts['ffws_element_question']] = $atts;
    }
    set_transient('ffws_faqs_with_schema_data',$ffws_schema_data);

    $ffws_id = !empty($atts['ffws_question_id'])?$atts['ffws_question_id']:rand();
    $ffws_class = !empty($atts['ffws_question_class'])?$atts['ffws_question_class']:"ffws_is_question";
    $qtag = !empty($atts['ffws_question_tag']) ? $atts['ffws_question_tag'] : "div";

    $element_class = ['fusion-no-small-visibility','fusion-no-medium-visibility','fusion-no-large-visibility'];
    $ffws_classes = explode(',',$atts['ffws_hide_on_mobile']);


    if(in_array('small-visibility',$ffws_classes)) {
        unset($element_class[0]);
    }
    if(in_array('medium-visibility',$ffws_classes)) {
        unset($element_class[1]);
    }
    if(in_array('large-visibility',$ffws_classes)) {
        unset($element_class[2]);
    }

    $element_class_ = implode(' ',$element_class);
    
    $output = '<div class="'.$element_class_.' ffws_schema_element">';
    $output .= '<'.$qtag.' id='.$ffws_id.' class="'.$ffws_class.' ffws_question">'.$atts['ffws_element_question'].'</'.$qtag.'>';
    $output .= $content;
    $output .= "</div>";
    
    return $output;
}
add_shortcode('ffws_faqs_with_schema','ffws_faqs_with_schema');

add_action('wp_footer', function(){
    $schema_data = get_transient('ffws_faqs_with_schema_data');
    if(!empty($schema_data)){
        $additional_types = get_option('ffws_option_additional_type');
        $image_url = wp_get_attachment_image_url( get_option('ffws_option_image'),'medium');
        $question_arr = [
            '@context'=>'https://schema.org',
            '@type'=>'FAQPage',
            '@id'=>get_option('ffws_option_id'),
            'specialty'=>get_option('ffws_option_speciality'),
            'accessmode'=>get_option('ffws_option_assessmode'),
            'additionaltype'=>$additional_types,
            'url'=>get_option('ffws_option_url'),
            'name'=>get_option('ffws_option_schema_name'),
            'image'=>$image_url
        ];
        $question_arr = array_filter($question_arr);
        $mainEntity = [];
        $i=0;
        foreach($schema_data as $datum) {
            $question = $datum['ffws_element_question'];
            $answer = $datum['element_schema_text'];
            $sameas = $datum['ffws_element_sameas'];
            $image = $datum['ffws_element_image'];
            $sameAsSrc = explode("\n", $sameas);
            $mainEntity[$i] = [
                    '@type'=>'Question',
                    'name'=>$question,
                    'acceptedAnswer'=>[
                            '@type'=>"Answer",
                            'text'=>$answer
                    ]
            ];
            if($image) {
                $mainEntity[$i]['acceptedAnswer']['image'] = $image;
            }
            if($sameas) {
                $mainEntity[$i]['acceptedAnswer']['sameAs'] = $sameAsSrc;
            }
            $i++;
        }
        $question_arr['mainEntity'] = $mainEntity;
    ?>
    <script type="application/ld+json">
        <?php echo json_encode($question_arr) ?>
    </script>
    <?php
    }
    delete_transient('ffws_faqs_with_schema_data');
});

add_action( 'admin_enqueue_scripts', 'ffws_load_wp_media_files' );
function ffws_load_wp_media_files( $page ) {
    if( $page == 'fusion-builder_page_ffws_settings_page' ) {
        wp_enqueue_media();
        wp_enqueue_script( 'ffws_script', plugins_url( '/js/script.js' , __FILE__ ));
    }
}

add_action('admin_footer', function() {
    ?>
    <script>
        jQuery(function ($) {
           $(document).on('blur','#element_schema_text', function() {
               this.value = this.value.split('"').join("'");
           });
        });
    </script>
    <?php
});
