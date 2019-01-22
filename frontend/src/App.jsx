import React, { Component } from 'react'
import { BrowserRouter, Route, Switch } from 'react-router-dom'
import MainPage from './components/MainPage'
import Login from './components/Login'
import Account from './components/Account'

export default class App extends Component {
  render() {
    return (
      <BrowserRouter>
        <Switch>
          <Route path={'/reestr'} component={MainPage}/>
          <Route path={'/bills'} component={MainPage}/>
          <Route path={'/print'} component={MainPage}/>
          <Route path={'/payments'} component={MainPage}/>
          <Route path={'/login'} component={Login}/>
          <Route path={'/account'} component={Account}/>
          <MainPage />
        </Switch>
      </BrowserRouter> 
    );
  }
}

