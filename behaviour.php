<?php
/**
 * stackmathgame question behaviour.
 *
 * Extends adaptivemultipart to:
 *  1. Suppress the "Check" submit button at render time.
 *  2. Suppress native .stackprtfeedback output containers.
 *  3. Suppress inline .stackinputfeedback validation hints.
 *  4. Inject data-smg-* attributes on the question wrapper so the
 *     local_stackmathgame JS can identify and drive the question.
 *  5. Preserve all STACK answer processing, sequencecheck management,
 *     partial credit, and attempt state handling from the parent.
 *
 * The behaviour deliberately does NOT touch any grading or attempt logic.
 * It is purely a rendering contract between STACK and the game layer.
 *
 * @package    qbehaviour_stackmathgame
 * @copyright  2025 Your Institution
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// The parent lives in question/behaviour/adaptivemultipart/behaviour.php.
require_once($CFG->dirroot . '/question/behaviour/adaptivemultipart/behaviour.php');

class qbehaviour_stackmathgame extends qbehaviour_adaptivemultipart {

    // -------------------------------------------------------------------------
    // Behaviour identity
    // -------------------------------------------------------------------------

    /**
     * The name shown in quiz settings → Question behaviour dropdown.
     * Translated via lang string 'pluginname'.
     */
    public function get_name(): string {
        return 'stackmathgame';
    }

    // -------------------------------------------------------------------------
    // Rendering overrides
    // -------------------------------------------------------------------------

    /**
     * Controls: suppress the Check button entirely.
     * The game JS submits answers via fetch(); no native button needed.
     *
     * @param question_attempt              $qa
     * @param question_display_options      $options
     * @return string  Empty string — no controls rendered.
     */
    public function controls(
        question_attempt         $qa,
        question_display_options $options
    ): string {
        return '';
    }

    /**
     * Feedback: suppress the native .outcome / .specificfeedback container.
     * The game JS reads feedback from the AJAX response and renders it
     * inside its own speech-bubble / notification system.
     *
     * We still allow STACK to write the feedback into the DOM (needed for
     * sequencecheck and attempt state), but we wrap it in a visually hidden
     * container that the JS can query when needed.
     *
     * @param question_attempt              $qa
     * @param question_display_options      $options
     * @return string  Visually hidden feedback wrapper.
     */
    public function feedback(
        question_attempt         $qa,
        question_display_options $options
    ): string {
        $parentFeedback = parent::feedback($qa, $options);
        if ($parentFeedback === '') {
            return '';
        }

        // Wrap in an sr-only / visually-hidden div.
        // The game JS can query .smg-native-feedback to read results
        // without those results being visible to the student.
        return html_writer::div(
            $parentFeedback,
            'smg-native-feedback visually-hidden',
            ['aria-hidden' => 'true']
        );
    }

    /**
     * Add data-smg-* attributes to the outer question <div>.
     * The game JS uses these to identify question type, slot, and behaviour.
     *
     * @param question_attempt              $qa
     * @param question_display_options      $options
     * @param int                           $number  Question number in quiz
     * @return array  Extra attributes for the question div.
     */
    public function question_outer_attributes(
        question_attempt         $qa,
        question_display_options $options,
        int                      $number
    ): array {
        $attrs = parent::question_outer_attributes($qa, $options, $number);

        // Signal to the game JS that this question is game-controlled.
        $attrs['data-smg-controlled']  = 'true';
        $attrs['data-smg-behaviour']   = 'stackmathgame';
        $attrs['data-smg-slot']        = (string) $qa->get_slot();
        $attrs['data-smg-questionid']  = (string) $qa->get_question_id();

        // Inputs: count of independent answer inputs (for multi-enemy assignment).
        $question = $qa->get_question();
        if (method_exists($question, 'get_expected_data')) {
            $inputcount = count(array_filter(
                array_keys($question->get_expected_data()),
                fn($k) => !str_ends_with($k, '_val') && !str_ends_with($k, '_type')
            ));
            $attrs['data-smg-inputcount'] = (string) $inputcount;
        }

        return $attrs;
    }

    // -------------------------------------------------------------------------
    // CSS suppression via inline <style> injection
    // -------------------------------------------------------------------------

    /**
     * Inject a minimal <style> block that hides elements the game doesn't want:
     *  – The native Check/Submit button (in case controls() is bypassed)
     *  – .stackinputfeedback validation hints (game shows these in its own UI)
     *
     * This is a safety net, not the primary mechanism. controls() returning ''
     * is the primary suppressor for the Check button.
     */
    public function render_before_question_summary(
        question_attempt         $qa,
        question_display_options $options
    ): string {
        static $styleInjected = false;
        if ($styleInjected) {
            return '';
        }
        $styleInjected = true;

        // These rules are scoped to questions with data-smg-controlled.
        $css = <<<CSS
[data-smg-controlled="true"] .im-controls { display:none !important; }
[data-smg-controlled="true"] .stackinputfeedback { display:none !important; }
[data-smg-controlled="true"] input[type=submit].submit,
[data-smg-controlled="true"] button[type=submit].submit { display:none !important; }
CSS;
        return html_writer::tag('style', $css, ['data-source' => 'qbehaviour_stackmathgame']);
    }
}
