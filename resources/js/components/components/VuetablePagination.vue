<template>
    <ul v-show="tablePagination && tablePagination.last_page > 1" :class="css.wrapperClass">
        <li :class="isOnFirstPage ? css.disabledClass : ''">
            <a @click="loadPage(1)">
                <i class="material-icons">first_page</i>
            </a>
        </li>
        <li :class="isOnFirstPage ? css.disabledClass : ''">
            <a @click="loadPage('prev')">
                <i class="material-icons">chevron_left</i>
            </a>
        </li>
        <template v-if="notEnoughPages">
            <template v-for="n in totalPage">
                <li :class="isCurrentPage(n) ? css.activeClass : ''">
                    <a @click="loadPage(n)"
                       v-html="n">
                    </a>
                </li>
            </template>
        </template>
        <template v-else>
            <template v-for="n in windowSize">
                <li :class="isCurrentPage(windowStart+n-1) ? css.activeClass : ''">
                    <a @click="loadPage(windowStart+n-1)"
                       v-html="windowStart+n-1">
                    </a>
                </li>
            </template>
        </template>
        <li :class="isOnLastPage ? css.disabledClass : ''">
            <a @click="loadPage('next')">
                <i class="material-icons">chevron_right</i>
            </a>
        </li>
        <li :class="isOnLastPage ? css.disabledClass : ''">
            <a @click="loadPage(totalPage)">
                <i class="material-icons">last_page</i>
            </a>
        </li>
    </ul>
</template>

<script>
    import PaginationMixin from 'vuetable-2/src/components/VuetablePaginationMixin'

    export default {
        mixins: [PaginationMixin],
    }
</script>
