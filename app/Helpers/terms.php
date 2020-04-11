<?php

use App\Term;
use App\Taxonomy;

function get_the_terms($post_id, $taxonomy = 'home-type')
{

}

function get_taxonomies()
{
    $tax = new Taxonomy();
    $taxObject = $tax->getAll();
    if (!empty($taxObject) && is_object($taxObject)) {
        $return = [];
        foreach ($taxObject as $taxonomy) {
            $return[$taxonomy->taxonomy_id] = $taxonomy->taxonomy_title;
        }
        return $return;
    } else {
        return [];
    }
}

function get_terms($taxonomy = 'home-type', $is_object = false, $trans = false)
{
    $return = [];
    $term = new Term();
    $tax = new Taxonomy();

    $taxObject = $tax->getByName($taxonomy);
    if (!empty($taxObject) && is_object($taxObject)) {
        $terms = $term->getTerms($taxObject->taxonomy_id);
        if ($is_object) {
            $return = $terms;
        } else {
            if ($terms) {
                foreach ($terms as $item) {
                	if($trans)
                        $return[$item->term_id] = esc_attr(get_translate($item->term_title));
                	else
		                $return[$item->term_id] = esc_attr($item->term_title);
                }
            }
        }
    }

    return $return;
}


function get_term_by($by = 'id', $term_id)
{
    $term_model = new Term();
    switch ($by) {
        case 'id':
        default:
            $term = $term_model->getById($term_id);
            break;
        case 'name':
            $term = $term_model->getByName($term_id);
    }

    return $term;
}
