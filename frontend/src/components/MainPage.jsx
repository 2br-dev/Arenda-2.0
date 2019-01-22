import React, { Component } from 'react'
import Navigation from './Navigation'
import Reestr from './Reestr'
import Bills from './Bills'
import Print from './Print'
import Payments from './Payments'

export default class MainPage extends Component {
  constructor(props) {
    super(props);
    this.state = {
      url: window.location.pathname.slice(1), // берём реальный URL
    }
    this.handleChangeUrl = this.handleChangeUrl.bind(this);
  }

  // передаём в навигацию, чтобы переключать стейт.
  handleChangeUrl = url => this.setState({ url });

  // переключаем компонент и отправляем в рендер
  switchComponent = () => {
    switch(this.state.url) {
      case '':
        return <Print />  
      case 'reestr':
        return <Reestr /> 
      case 'bills':
        return <Bills /> 
      case 'print':
        return <Print /> 
      case 'payments':
        return <Payments /> 
      default: 
        console.log('default');  
    }     
  }

  render() {
    const { url } = this.state

    return (
      <React.Fragment>
          <Navigation 
            url={url} 
            handleChangeUrl={this.handleChangeUrl}
          />
          {this.switchComponent()}  
      </React.Fragment>
    )
  }
}
