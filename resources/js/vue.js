/*jshint esversion: 6 */

require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import axios from 'axios';
import VueSocketIO from 'vue-socket.io';

////// INSTANCES //////
const myScripts = require('../../public/js/scripts');

////// DEPENDENCIES //////
Vue.use(VueRouter);
Vue.use(Vuex);
Vue.config.devtools = true;
Vue.use(new VueSocketIO({
    debug: true,
    connection: 'http://127.0.0.1:8080',
}))

////// COMPONENTS //////
const index = Vue.component('index', require('./components/index'));
const menuList = Vue.component('menu-list', require('./components/menu/menu'));
const loginForm = Vue.component('login-form', require('./components/worker/login'));
const logout = Vue.component('logout', require('./components/worker/logout'));
const meals = Vue.component('meals', require('./components/meals/list'));
const tables = Vue.component('tables', require('./components/tables/list'));
const invoices = Vue.component('invoices', require('./components/invoices/list'));
const workers = Vue.component('workers', require('./components/worker/list'));
const workersPassword = Vue.component('password', require('./components/worker/password'));
const profile = Vue.component('profile', require('./components/worker/profile'));
const statistics = Vue.component('statistics', require('./components/graphics/statistics'));

////// ROUTES //////
const router = new VueRouter({
    mode: 'history',
    routes: [
            {path: '/', component: index},
            {path: '/menus', component: menuList},
            {path: '/login',component: loginForm},
            {path: '/workers',component: workers},
            {path: '/logout',component: logout},
            {path: '/meals',component: meals},
            {path: '/tables',component: tables},
            {path: '/invoices',component: invoices},
            {path: '/profile',component: profile},
            {path: '/workers/password/:token',component: workersPassword},
            {path: '/statistics',component: statistics},
        ]
});

router.beforeEach((to, from, next) => {
    myScripts.startInstances();
    next();
});

////// VUEX //////
export const store = new Vuex.Store({
    state:{
        token: sessionStorage.getItem('access_token') || null,
        user: JSON.parse(sessionStorage.getItem('logged_user')) || null,
    },
    mutations:{
        retrieveToken(state, token) {
            state.token = token;
        },
        retrieveUser(state, user) {
            state.user = user;
        },
        destroyToken(state){
            state.token = null;
            state.user = null;
        }
    },
    getters:{
        loggedIn(state){
            return state.token != null;
        }
    },
    actions:{
        login(context, credentials){
            return new Promise((resolve, reject) => {
                axios.post('/api/login',{
                    email: credentials.email,
                    password: credentials.password
                })
                    .then((res) => {
                        const token = res.data.access_token;
                        context.commit('retrieveToken', token);
                        axios.defaults.headers.common.Authorization = 'Bearer '+ context.state.token;

                        axios.get('/api/user')
                            .then(res => {
                                app.$socket.emit('user_enter', res.data);
                                context.commit('retrieveUser', res.data);
                                sessionStorage.setItem('logged_user', JSON.stringify(res.data));
                            })
                            .catch(res => {
                                console.log(res);
                            });
                        sessionStorage.setItem('access_token', token);
                        resolve(res);
                    })
                    .catch((res) => {
                        console.log(res);
                        reject(res);
                    })
            })
        },
        logout: function(context){
            if(context.getters.loggedIn){
                return new Promise((resolve,rejected) => {
                    axios.post('/api/logout')
                        .then((res) => {
                            context.commit('destroyToken');
                            sessionStorage.removeItem('access_token');
                            sessionStorage.removeItem('logged_user');
                            axios.defaults.headers.common.Authorization = null;
                            resolve(res);
                        })
                        .catch((res) => {
                            context.commit('destroyToken');
                            sessionStorage.removeItem('access_token');
                            sessionStorage.removeItem('logged_user');
                            axios.defaults.headers.common.Authorization = null;
                            rejected(res);
                        })
                })
            }
        },
        updateUser(context, user){
            sessionStorage.removeItem('logged_user');
            sessionStorage.setItem('logged_user', JSON.stringify(user));
        },
    }
});

const app = new Vue({
    router: router,
    store: store,
    data: function(){
        return {
            problem: '',
            user: this.$store.state.user,
        }
    },
    methods: {
        isLogged: function () {
            return this.user != null;
        },
        sendNotification(){
            this.$socket.emit('problem', this.problem);
        }
    },
    sockets:{
        connect(){
            console.log('socket connected (socket ID = '+this.$socket.id+')');
            if(this.$store.state.token !== null){
                this.$socket.emit('user_enter', this.$store.state.user);
                if(this.$store.state.user.shift_active){
                    this.$socket.emit('shift_start',  this.$store.state.user);
                }
            }
        },
        meal_terminated(meal){
            M.toast({html:"The meal on table #"+meal.table_number+" has been terminated."})
        },
        meal_paid(meal){
            M.toast({html:"The meal on table #"+meal.table_number+" has been paid."})
        },
        notify_problem(problem){
            console.log(problem);
            M.toast({html:problem})
        },
        internShiftStart(name){
            M.toast({html: name + " started shift"})
        },
        moreThan5Invoice(message){
            M.toast({html: "An Invoice was paid with more than 5 customers"})
        }
    },
    created(){
        axios.defaults.headers.common.Authorization = 'Bearer ' + this.$store.state.token;
    }
}).$mount('#app');
