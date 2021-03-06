<?php
/**
 * Template for displaying progress of single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/progress.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$course = LP_Global::course();
$user   = LP_Global::user();

if ( ! $course || ! $user ) {
	return;
}

if ( ! $user->has_enrolled_course( $course->get_id() ) ) {
	return;
}

$course_data       = $user->get_course_data( $course->get_id() );
$course_results    = $course_data->get_results( false );
$passing_condition = $course->get_passing_condition();
$quiz_false        = $course_results['items']['quiz']['completed'] - $course_results['items']['quiz']['passed'];
?>

<div class="course-results-progress">
	<div class="items-progress">
		<h4 class="items-progress__heading">
			<?php esc_html_e( 'Lessons completed:', 'learnpress' ); ?>
		</h4>
		<span class="number"><?php printf( __( '%1$d/%2$d', 'learnpress' ), $course_results['items']['lesson']['completed'], $course_results['items']['lesson']['total'] ); ?></span>
	</div>

	<div class="items-progress">

		<h4 class="items-progress__heading">
			<?php esc_html_e( 'Quizzes submitted:', 'learnpress' ); ?>
		</h4>
		<span class="number" title="<?php esc_attr_e( sprintf( __( 'Failed %1$d, Passed %2$d', 'learnpress' ), $quiz_false, $course_results['items']['quiz']['passed'] ) ); ?>"><?php printf( __( '%1$d/%2$d', 'learnpress' ), $course_results['items']['quiz']['completed'], $course_results['items']['quiz']['total'] ); ?></span>
	</div>

	<?php do_action( 'learn-press/user-item-progress' ); ?>

	<div class="course-progress">
		<h4 class="items-progress__heading">
			<?php esc_attr_e( 'Course progress:', 'learnpress' ); ?>
		</h4>

		<div class="lp-course-status">
			<span class="number"><?php echo round( $course_results['result'], 2 ); ?><span class="percentage-sign">%</span></span>
		</div>

		<div class="learn-press-progress lp-course-progress <?php echo $course_data->is_passed() ? ' passed' : ''; ?>" data-value="<?php echo $course_results['result']; ?>" data-passing-condition="<?php echo $passing_condition; ?>" title="<?php echo esc_attr( learn_press_translate_course_result_required() ); ?>">
			<div class="progress-bg lp-progress-bar">
				<div class="progress-active lp-progress-value" style="left: <?php echo $course_results['result']; ?>%;">
				</div>
			</div>
			<div class="lp-passing-conditional" data-content="<?php printf( esc_html__( 'Passing condition: %s%%', 'learnpress' ), $passing_condition ); ?>" style="left: <?php echo $passing_condition; ?>%;">
			</div>
		</div>
	</div>

</div>
