import React, { Component } from 'react'
import './css/Login.css'
import Modal from './Modal'
import $ from 'jquery'
import Functions from '../functions/Functions'

export default class Login extends Component {
  state = {
    login: '',
    password: '',
    modal: false,
    modalText: '',
    error: false
  }

  // ставим в стейт введёные в инпут значения
  handleChange = event => this.setState({ [event.target.name]: event.target.value, error: false});
  // ф-я показывающая модальное окно
  openModal = (text) => this.setState({ modal: true, modalText: text});
  // передаём в Modal для переключения видимости
  toggleModal = () => this.setState({ modal: false, modalText: ''});

  // запрос на авторизацию
  handleSubmit = event => {
    event.preventDefault();
    const self = this;
    const { login, password } = this.state;
    const data = {
      password: password,
      login: login,
    };
    
    if (data.password && data.login) {
      $.ajax({
        type: "POST",
        url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/user/login.php`,
        data: data,
        success: function(res){        
          if (res.result === 1) {
            const F = new Functions();
            F.setCookie('user_id', res.id, 10);
            window.location.href = '/'; // если ок - на главную
          } else {
            self.setState({ error: true }) // ошибка
          }
          self.setState({ login: '', password: '' }) // ресет
          $('#authorization').trigger("reset"); 
        },
        error: function(err) {
          console.log(err);
        }
      });
    } else {
      self.openModal('Заполните все поля');
    }
  }

  render() {
    return (
      <React.Fragment>
        <div className='grid'>
          <div id='login'>
            <h2><span className='fontawesome-lock'></span>Войти в систему</h2>
            <form action='' method='POST' id='authorization' onSubmit={this.handleSubmit.bind(this)}>
              <fieldset>
                <p><label htmlFor='login'>Логин</label></p>
                <p><input 
                  onChange={this.handleChange} 
                  type='text' className='form-input' name='login' placeholder='Введите ваш логин' /></p>
                <p><label htmlFor='password'>Пароль</label></p>
                <p><input 
                  onChange={this.handleChange}
                  type='password' className='form-input' name='password' placeholder='Введите ваш пароль' /></p>
                <p><input type='submit' value='Войти' id='submit' /></p>
              </fieldset>
            </form>
          </div>
        </div>
        {this.state.error 
        ? 
        (<div className='error'>
          <div className='error-msg'>
            <img src='/img/warning_white_48x48.png' alt='' />
            <div className='error-msg-text'>
              <p><strong>Неправильная пара логин-пароль! Авторизоваться не удалось</strong></p>
              <p>Возможно у вас выбрана другая раскладка клавиатуры или нажата клавиша 'Caps Lock'.<br />Попробейте авторизоваться ещё раз.</p>
            </div>
          </div>    
        </div>)
        :null}

        {this.state.modal ? <Modal text={this.state.modalText} visible={this.state.modal} toggleModal={this.toggleModal} /> : null}
      </React.Fragment>
    )
  }
}
