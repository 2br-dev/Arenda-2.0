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
      url: ''
    }
    this.handleChangeUrl = this.handleChangeUrl.bind(this);
  }
  
  handleChangeUrl = url => this.setState({ url });

  // переключаем компонент и отправляем в рендер
  switchComponent = () => {
    switch(this.state.url) {
      case '':
        return <Reestr />  
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
      <>
        <Navigation 
          url={url} 
          handleChangeUrl={this.handleChangeUrl}
        />

        {this.switchComponent()}   
      </>
    )
  }
}
