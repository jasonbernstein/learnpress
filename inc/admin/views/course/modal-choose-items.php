<?php
/**
 * Template choose items.
 *
 * @since 3.0.0
 */

learn_press_admin_view( 'course/added-items-preview' );
learn_press_admin_view( 'course/pagination' );

?>

<script type="text/x-template" id="tmpl-lp-course-choose-item">
    <li class="section-item" :class="[item.type, item.added ? 'added' : 'addable']" @click="add">
        <input type="checkbox" :checked="item.added === true">
        <span class="title">{{item.title}}</span>
    </li>
</script>

<script type="text/javascript">
    (function (Vue, $store) {

        Vue.component('lp-course-choose-item', {
            template: '#tmpl-lp-course-choose-item',
            props: ['item', 'added'],
            watch: {
                added: function () {
                    this.$forceUpdate();
                }
            },
            methods: {
                add: function () {
                    if (this.item.added) {
                        return this.remove();
                    }

                    this.$emit('add', this.item);
                },
                remove: function () {
                    this.$emit('remove', this.item);
                }
            }
        });

    })(Vue, LP_Curriculum_Store);
</script>


<script type="text/x-template" id="tmpl-lp-course-choose-items">
    <div id="lp-modal-choose-items" :class="{show:show, loading: loading}">
        <div class="lp-choose-items" :class="{'show-preview': showPreview}">
            <div class="header">
                <div class="preview-title">
                    <span><?php esc_html_e( 'Selected items', 'learnpress' ); ?> ({{addedItems.length}})</span>
                </div>

                <ul class="tabs">
                    <template v-for="(type, key) in types">
                        <li :data-type="key"
                            class="tab"
                            @click.prevent="changeTab(key)"
                            :class="key === tab ? 'active': 'inactive'">
                            <a href="#" @click.prevent="">{{type}}</a>
                        </li>
                    </template>
                </ul>

                <div class="close" @click="close">
                    <span class="dashicons dashicons-no-alt"></span>
                </div>
            </div>
            <div class="main">
                <form class="search" @submit.prevent="">
                    <input placeholder="<?php esc_attr_e( 'Type here to search item', 'learnpress' ); ?>"
                           title="search"
                           @input="onChangeQuery"
                           v-model="query">
                </form>

                <ul class="list-items">
                    <template v-if="!items.length">
                        <div><?php esc_html_e( 'No any item.', 'learnpress' ); ?></div>
                    </template>

                    <template v-for="item in items">
                        <lp-course-choose-item
                                @add="addItem"
                                @remove="removeItem"
                                :added="item.added"
                                :item="item"></lp-course-choose-item>
                    </template>
                </ul>

                <lp-pagination :total="totalPage" @update="changePage"></lp-pagination>
                <lp-added-items-preview :show="showPreview"></lp-added-items-preview>
            </div>

            <div class="footer">
                <div class="cart">
                    <button
                            @click="checkout"
                            :disabled="!addedItems.length || adding"
                            type="button"
                            class="button button-primary checkout">
                        <span v-if="!adding"><?php esc_html_e( 'Add', 'learnpress' ); ?></span>
                        <span v-if="adding"><?php esc_html_e( 'Adding', 'learnpress' ); ?></span>
                    </button>

                    <button type="button"
                            :disabled="!addedItems.length || adding"
                            @click.prevent="showPreview = !showPreview"
                            class="button button-secondary edit-selected">
                        {{textButtonEdit}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/javascript">
    (function (Vue, $store, $) {

        Vue.component('lp-curriculum-choose-items', {
            template: '#tmpl-lp-course-choose-items',
            data: function () {
                return {
                    query: '',
                    page: 1,
                    tab: 'lp_lesson',
                    delayTimeout: null,
                    showPreview: false,
                    adding: false
                };
            },
            created: function () {
                var vm = this;

                $store.subscribe(function (mutation) {
                    if (!mutation || mutation.type !== 'ci/TOGGLE') {
                        return;
                    }

                    if (vm.show) {
                        vm.init();

                        $('body').addClass('lp-modal-choose-items-open');
                    } else {
                        $('body').removeClass('lp-modal-choose-items-open');
                    }
                });
            },
            methods: {
                init: function () {
                    this.query = '';
                    this.page = 1;
                    this.tab = this.firstType;
                    this.showPreview = false;
                    this.adding = false;
                    this.makeSearch();
                },

                checkout: function () {
                    this.adding = true;
                    $store.dispatch('ci/addItemsToSection');
                },

                changePage: function (page) {
                    if (page === this.page) {
                        return;
                    }

                    this.page = page;
                    this.makeSearch();
                },

                addItem: function (item) {
                    $store.dispatch('ci/addItem', item);
                },

                removeItem: function (item) {
                    $store.dispatch('ci/removeItem', item);
                },

                close: function () {
                    $store.dispatch('ci/toggle');
                },

                changeTab: function (key) {
                    if (key === this.tab) {
                        return;
                    }

                    this.tab = key;
                    this.page = 1;
                    this.makeSearch();
                },

                onChangeQuery: function () {
                    var vm = this;

                    if (this.delayTimeout) {
                        clearTimeout(this.delayTimeout);
                    }

                    this.delayTimeout = setTimeout(function () {
                        vm.page = 1;
                        vm.makeSearch();
                    }, 500);
                },

                makeSearch: function () {
                    $store.dispatch('ci/searchItems', {
                        query: this.query,
                        page: this.page,
                        type: this.tab
                    });
                }
            },
            computed: {
                status: function () {
                    return $store.getters['ci/status'];
                },
                loading: function () {
                    return this.status === 'loading';
                },
                textButtonEdit: function () {
                    if (this.showPreview) {
                        return $store.getters['i18n/all'].back;
                    }

                    return $store.getters['i18n/all'].selected_items + ' (' + this.addedItems.length + ')';
                },
                addedItems: function () {
                    return $store.getters['ci/addedItems'];
                },
                pagination: function () {
                    return $store.getters['ci/pagination'];
                },
                totalPage: function () {
                    if (this.pagination) {
                        return parseInt(this.pagination.total) - 1 || 1;
                    }

                    return 1;
                },
                items: function () {
                    return $store.getters['ci/items'];
                },
                show: function () {
                    return $store.getters['ci/isOpen'];
                },
                types: function () {
                    return $store.getters['ci/types'];
                },
                firstType: function () {
                    for (var type in $store.getters['ci/types']) {
                        return type;
                    }

                    return false;
                }
            }
        });

    })(Vue, LP_Curriculum_Store, jQuery);
</script>
