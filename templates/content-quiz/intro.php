<?php
/**
 * Template for displaying quiz's introduction
 *
 * @package LearnPress/Templates
 * @author  ThimPress
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$course = LP_Global::course();
$quiz   = LP_Global::course_item_quiz();
$count  = $quiz->get_retake_count();
?>

<ul class="quiz-intro">
    <li>
        <label><?php _e( 'Attempts allowed', 'learnpress' ); ?></label>
		<span><?php echo ( null == $count || 0 > $count ) ? __( 'Unlimited', 'learnpress' ) : ( $count ? $count : __( 'No', 'learnpress' ) ); ?></span>
    </li>
    <li>
        <label><?php _e( 'Duration', 'learnpress' ); ?></label>
        <span><?php echo $quiz->get_duration_html(); ?></span>
    </li>
    <li>
        <label><?php _e( 'Passing grade', 'learnpress' ); ?></label>
        <span><?php echo sprintf( '%d%%', $quiz->get_passing_grade() ); ?></span>
    </li>
    <li>
        <label><?php _e( 'Questions', 'learnpress' ); ?></label>
        <span><?php echo $quiz->get_total_questions(); ?></span>
    </li>
</ul>