import { Link } from 'react-router-dom'
import React, { Component } from 'react'
import './css/Navigation.css'
import styled from 'styled-components'

// компонент навигации
// навигация и больше ничего, лаконично и красиво.

export default class Navigation extends Component {

  logout = () => {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/user/logout.php`)
      .then(() => window.location.href = '/login')
      .catch(err => console.log(err))
  }

  render() {
    return (
      <header>
        <input type="checkbox" className="hamburger" id="hamburger" />
        <label htmlFor="hamburger" className="icon"></label>
        <nav role='navigation'>
          <ul>
            {window.location.pathname !== '/account' ? (<React.Fragment>
              <li>
                <Link to='/data' onClick={() => this.props.handleChangeUrl('data')}>Данные</Link>
              </li>
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
                <Link to='/payments' onClick={() => this.props.handleChangeUrl('payments')}>Оплаты</Link>
              </li>
              <li>
                <Link to='/history' onClick={() => this.props.handleChangeUrl('history')}>История</Link>
              </li>
              {/*    <li>
                <Link to='/stats' onClick={() => this.props.handleChangeUrl('stats')}>Статистика</Link>
              </li> */}
            </React.Fragment>) : null }
            <Li>
              <Link to='' onClick={this.logout.bind(this)}>Выйти</Link>
            </Li>
          </ul>
        </nav>
      </header>
    )
  }
}

const Li = styled.li`
  a {
    color: #fff;
  }
  background: #000;
`;