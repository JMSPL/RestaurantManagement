<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/materialize.min.css') }}" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <main>
       <div id="app">
           <!-- Dropdown Structure -->
           <ul id="user-menu" class="dropdown-content">
               <li v-if="user.type === 'manager'"><router-link to="/workers">Workers</router-link></li>
               <li v-if="user.type === 'manager'"><router-link to="/tables">Tables</router-link></li>
               <li v-if="user.type === 'manager' || user.type === 'waiter'"><router-link to="/meals">Meals</router-link></li>
               <li v-if="user.type === 'manager'"><router-link to="/invoices">Invoices</router-link></li>
               <li v-if="user.type === 'cashier'"><router-link to="/invoices">Pending Invoices</router-link></li>
               <li v-if="user.type === 'manager'"><router-link to="/statistics">Statistics</router-link></li>
               <li class="divider"></li>
               <li><router-link to="/profile">Profile</router-link></li>
               <li class="divider"></li>
               <li><router-link to="/logout">Logout</router-link></li>
           </ul>
           <nav>
               <div class="nav-wrapper">
                   <div class="container">
                       <router-link to="/" class="brand-logo">{{ config('app.name', 'Laravel') }}</router-link>
                       <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                       <ul id="nav-mobile" class="right hide-on-med-and-down">
                           <li><router-link to="/menus">Menus</router-link></li>
                           <li v-if="isLogged()"><a class="dropdown-trigger" href="#!" data-target="user-menu">@{{ user.name }}<i class="material-icons right">arrow_drop_down</i></a></li>
                           <li v-if="isLogged() && user.shift_active === 1"><a href="#">
                                   <span class="new badge red" style="float: none;" data-badge-caption="">In Shift</span>
                               </a></li>
                           <li v-if="!isLogged()"><router-link to="/login">Login</router-link></li>
                       </ul>
                   </div>
               </div>
           </nav>
           <div class="container">
               <div class="row">
                   <router-view></router-view>
               </div>
           </div>

           <!-- Dropdown Structure -->
           <ul id="user-menu-mobile" class="dropdown-content">
               <li v-if="user.type === 'manager'"><router-link to="/workers">Workers</router-link></li>
               <li v-if="user.type === 'manager'"><router-link to="/tables">Tables</router-link></li>
               <li v-if="user.type === 'manager'"><router-link to="/meals">Meals</router-link></li>
               <li v-if="user.type === 'manager'"><router-link to="/invoices">Invoices</router-link></li>
               <li class="divider"></li>
               <li><router-link to="/profile">Profile</router-link></li>
               <li class="divider"></li>
               <li><router-link to="/logout">Logout</router-link></li>
           </ul>
           <ul class="sidenav" id="mobile-demo">
               <li><router-link to="/menus">Menus</router-link></li>
               <li v-if="isLogged()"><a class="dropdown-trigger" href="#!" data-target="user-menu-mobile">@{{ user.name }}<i class="material-icons right">arrow_drop_down</i></a></li>
               <li v-if="isLogged() && user.shift_active === 1"><a href="#">
                       <span class="new badge red center" style="float: none;" data-badge-caption="">In Shift</span>
                   </a></li>
               <li v-if="!isLogged()"><router-link to="/login">Login</router-link></li>
           </ul>
           <div class="fixed-action-btn" v-if="isLogged() && user.shift_active">
               <a class="btn-floating btn-large modal-trigger waves-effect" href="#modal1">
                   <i class="large material-icons">mode_edit</i>
               </a>
           </div>
           <!-- Modal Structure -->
           <div id="modal1" class="modal">
               <div class="modal-content">
                   <h4>Problem</h4>
                   <div class="input-field col s12">
                       <textarea id="textarea1" class="materialize-textarea" v-model="problem"></textarea>
                       <label for="textarea1">Description</label>
                   </div>
               </div>
               <div class="modal-footer">
                   <a href="#!" class="modal-close waves-effect waves-red btn-flat">Close</a>
                   <a href="#!" class="modal-close waves-effect waves-green btn-flat" @click="sendNotification">Send</a>
               </div>
           </div>
       </div>
    </main>


    <script type="text/javascript" src="{{ asset('js/materialize.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
</body>
</html>
