<?php
/**
 * Admin Quiz Editor: Editor template.
 *
 * @since 3.0.0
 */

learn_press_admin_view( 'quiz/questions' );
learn_press_admin_view( 'quiz/modal-choose-items' );
?>

<script type="text/x-template" id="tmpl-lp-quiz-editor">
    <div id="admin-quiz-editor" class="learn-press-box-data lp-admin-editor" :class="{'need-reload': !heartbeat}">
        <div class="lp-box-data-head heading">
            <h3><?php echo __( 'Questions', 'learnpress' ); ?><span class="status" :class="status"></span></h3>
            <span :class="['collapse-list-questions dashicons ' , close ? 'dashicons-arrow-down' : 'dashicons-arrow-up']"
                  @click="toggle"></span>
        </div>
        <div class="lp-box-data-content">
            <div class="lp-list-questions">
                <div class="header">
                    <div class="table-row">
                        <div class="lp-column-sort"></div>
                        <div class="lp-column-order">#</div>
                        <div class="lp-column-name"><?php esc_html_e( 'Name', 'learnpress' ); ?></div>
                        <div class="lp-column-type"><?php esc_html_e( 'Type', 'learnpress' ); ?></div>
                        <div class="lp-column-actions"><?php esc_html_e( 'Actions', 'learnpress' ); ?></div>
                    </div>
                </div>

                <form @submit.prevent="">
                    <lp-quiz-questions></lp-quiz-questions>
                </form>

                <div class="footer">
                    <div class="table-row">
                        <div class="add-new-question">
                            <div class="title">
                                <input type="text" v-model="newQuestion.title">
                            </div>
                            <div class="add-new">
                                <button type="button" class="button" :disabled="!addableNew"
                                        @click.prevent="addItem(newQuestion.type)"
                                        @keyup.enter.prevent="addItem(newQuestion.type)">
									<?php esc_html_e( 'Add as New', 'learnpress' ); ?>
                                </button>
                                <ul class="question-types">
                                    <li v-for="(type, key) in questionTypes">
                                        <a href="#" :data-type="key" @click.prevent="addItem(key)">{{type}}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="select-item">
                                <button type="button" class="button" @click.prevent="openModal">
									<?php esc_html_e( 'Select', 'learnpress' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <lp-quiz-choose-items></lp-quiz-choose-items>
        </div>

        <div class="notify-reload">
            <div class="inner"><?php esc_html_e( 'Something went wrong! Please reload to keep editing list quiz questions.', 'learnpress' ); ?></div>
        </div>
    </div>
</script>

<script type="text/javascript">
    (function (Vue, $store, $) {

        Vue.component('lp-quiz-editor', {
            template: '#tmpl-lp-quiz-editor',
            data: function () {
                return {
                    newQuestion: {
                        'title': '',
                        'type': 'true_or_false'
                    }
                }
            },
            created: function () {
                setInterval(function () {
                    $store.dispatch('heartbeat');
                }, 60 * 1000);
            },
            computed: {
                heartbeat: function () {
                    return $store.getters['heartbeat'];
                },
                // editor status
                status: function () {
                    return $store.getters.status;
                },
                // list questions close
                close: function () {
                    return $store.getters['lqs/isHiddenListQuestions'];
                },
                // quiz id
                id: function () {
                    return $store.getters['id'];
                },
                // addable new
                addableNew: function () {
                    return !!this.newQuestion.title;
                },
                // all question types
                questionTypes: function () {
                    return $store.getters['questionTypes'];
                }
            },
            methods: {
                // toggle all questions
                toggle: function () {
                    $store.dispatch('lqs/toggleAll');
                },
                // add new question
                addItem: function (type) {
                    this.newQuestion.type = type;
                    $store.dispatch('lqs/newQuestion', this.newQuestion);
                    this.newQuestion.title = '';
                },
                // open modal
                openModal: function () {
                    $store.dispatch('cqi/open', parseInt(this.quizId));
                }
            }
        })

    })(Vue, LP_Quiz_Store, jQuery);
</script>