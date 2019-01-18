import { Link } from 'react-router-dom'
import React, { Component } from 'react'
import './css/Navigation.css'

// компонент навигации
// навигация и больше ничего, лаконично и красиво.

export default class Navigation extends Component {
  render() {
    return (
      <header>
        <input type="checkbox" className="hamburger" id="hamburger" />
        <label htmlFor="hamburger" className="icon"></label>
        <nav role='navigation'>
          <ul>
            <li>
              <Link to='/reestr' onClick={() => this.props.handleChangeUrl('reestr')}>Реестр арендаторов</Link>
            </li>
            <li>
              <Link to='/bills' onClick={() => this.props.handleChangeUrl('bills')}>Выставление счетов</Link>
            </li>
            <li>
              <Link to='/print' onClick={() => this.props.handleChangeUrl('print')}>Печать счетов</Link>
            </li>
            <li>
              <Link to='/payments'onClick={() => this.props.handleChangeUrl('payments')}>Оплаты</Link>
            </li> 
          </ul>
        </nav>
      </header>
    )
  }
}
