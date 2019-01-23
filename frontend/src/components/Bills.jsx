import React, { Component } from 'react'
import { connect } from 'react-redux'
import $ from 'jquery'
import styled from 'styled-components'
import Loader from './Loader'
import Modal from './Modal'

class Bills extends Component {
  constructor(props){
    super(props);
    this.state = {
      fromFirstValue: 1,
      today: this.setTodayDate(), // сегодняшняя дата
      loading: true,
      modal: false,
      modalText: ''
    }
    this.openModal    = this.openModal.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  // получаем договора 
  componentDidMount() {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/contract/read_all.php`)
      .then(response => response.json())
      .then(res => this.props.getContracts(res)) 
      .then(() => this.setState({ loading: false }))
  }

  // выставление счёта
  handleSubmit(e) {
    e.preventDefault();
    let self = this,
        allRenters = [],
        allId = [],
        allSum = [],
        index = 0;

    self.setState({ modal: false })

    // для каждого отмеченного арендодателя добавляем в массив его:
    // ID, renter, summa 
    $("input[name='renter']:checked").each(function() {
      allRenters.push($(this).val());
      allId.push($(this).data('id'));
      allSum.push($(this).data('sum'));
    });
    
    const modifiedArr = [];
    $("input[name='period_sum']:enabled").each(function() {
      if ($(this).val() === '') {
        index++;
      } else {
        allSum[index] = $(this).val();
        index++;
        modifiedArr.push($(this).data('id'));
      }
    }); 

    // переводим в нужный формат цифру для SQL
    allSum = allSum.map(sum => parseFloat(sum.replace(/,/g,'.')).toFixed(2));

    // данные
    const data = {
      year:               $("input[name='year']:checked").val(),
      month:              $("input[name='month']:checked").val(),
      renter:             allRenters,
      from_first:         $("input[name='from_first']:checked").val(),
      from_first_number:  $("input[type='number']").val(),
      date:               $("input[name='date']").val(),
      summa_id:           allId,
      period_sum:         allSum,
      modified:           modifiedArr
    };

    console.log(data);
    // если все поля заполнены делаем запрос
    if (data.month && data.year && data.renter.length !== 0) {
      $("input[name='period_sum']").prop('disabled', true);         
      $.ajax({
        type: "POST",
        url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/actions/vystavlenie_scheta.php`,
        data: data,
        success: function(res){
          if (res.result === 1) {
            $('#vystavlenie-schetov').trigger("reset"); // при успехе ресет формы
            self.openModal('Счёт выставлен успешно!'); // говорим пользователю
          } else {
            self.openModal('Что-то пошло не так.'); // если нет - говорим пользователю
          }
        },
        error: function(err) {
          console.log(err);
        }
      });
    } else {
      self.openModal('Пожалуйста, заполните все поля'); // если нет - подсказка пользователю
    }
  
  }

  // ф-я показывающая модальное окно
  openModal(text) {
    this.setState({ modal: true, modalText: text});
  }
  // передаём в Modal для переключения видимости
  toggleModal = () => this.setState({ modal: false, modalText: ''});

  // обрабатываем значение 
  handleChangeFromFirstValue = e => this.setState({ fromFirstValue: e.target.value }); 

  // ф-я выставления сегодняшней даты
  setTodayDate = () => {
    let date = new Date(),
        day = date.getDate(),
        month = date.getMonth() + 1,
        year = date.getFullYear();
    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;
    return year + '-' + month + '-' + day;
  }

  
  // убираем дизейблед с инпута при выборе арендатора
  handleCheck = e => {
    const makeEnabled = $(e.target).data('id');
    if ( $(e.target).prop('checked') ) {
      $(`input[name='period_sum'][data-id=${makeEnabled}]`).prop('disabled', false);
    } else {
      $(`input[name='period_sum'][data-id=${makeEnabled}]`).prop('disabled', true);
    }
  }
  
  // создаем радио баттон
  createRadio(value, name) {
    return (
      <label className='container-radio'>{value}
        <input name={name} type='radio' value={value} />
        <span className='checkmark'></span>
      </label>
    )
  }

  // создаем чекбокс
  createCheckbox(name, number, sum, id, renter, key) {
    return (
      <ContractRow key={key}>
        <label className='container-checkbox'><b>{name}</b> - {number}
          <input type='checkbox' name='renter' data-sum={sum} data-id={id} value={renter} onClick={this.handleCheck} />
          <span className='checkmark'></span>
        </label>
        <div>
          <p>По договору: <b>{sum}₽</b></p>
          <input type='text' data-id={id} disabled={true} name='period_sum' />
        </div>
      </ContractRow>
    )
  }

  render() {
    const { fromFirstValue, loading } = this.state;

    return (
      <div className='container'>

      {/* если лоадинг, то показываем лоадер, иначе компонент */}
      {loading ? <Loader></Loader> : <>
      <DateInput>
        <p className='date-error'>Выберите дату:</p>
        <input type='date' name='date' value={this.state.today} id='date' onChange={e => this.setState({today: e.target.value})}/>
      </DateInput>
      <hr/>
      <Form onSubmit={this.hadleSubmit} id='vystavlenie-schetov' method='POST' action=''>
        <p className='error renter-error'><strong>Выберите один или более договор:</strong></p>      
          
        {this.props.testStore.contracts.map((contract, index) => {           
          return this.createCheckbox(contract.short_name, contract.contract_number, contract.summa, contract.contract_id, contract.renter_id, index) 
        })}

        <hr />
        <span className='error year-error'><strong>Выберите год:</strong></span>
        <YearsContainer>
          {this.createRadio('2016', 'year')}
          {this.createRadio('2017', 'year')}
          {this.createRadio('2018', 'year')}
          {this.createRadio('2019', 'year')}
          {this.createRadio('2020', 'year')}
        </YearsContainer>
        <hr />
        <span className='error month-error'><strong>Выберите месяц:</strong></span>
        <MonthsContainer>
          <span>
            {this.createRadio('Январь', 'month')}
            {this.createRadio('Февраль', 'month')}
            {this.createRadio('Март', 'month')}
          </span>
          <span>
            {this.createRadio('Апрель', 'month')}
            {this.createRadio('Май', 'month')}
            {this.createRadio('Июнь', 'month')}
          </span>
          <span>
            {this.createRadio('Июль', 'month')}
            {this.createRadio('Август', 'month')}
            {this.createRadio('Сентябрь', 'month')}                                  
          </span>
          <span>
            {this.createRadio('Октябрь', 'month')}
            {this.createRadio('Ноябрь', 'month')}
            {this.createRadio('Декабрь', 'month')}                 
          </span>
        </MonthsContainer>
        <hr />
        <FromFirst>
          <label className='container-checkbox'><strong>Начать нумерацию с</strong> 
            <input type='checkbox' name='from_first' />
            <span className='checkmark'></span>
          </label>
          <input name='from_first_number' type='number' value={fromFirstValue} onChange={this.handleChangeFromFirstValue} />        
        </FromFirst>  
      </Form>
      <hr />
  
      <button className='btn' type='submit' onClick={this.handleSubmit}>Выставить счёт</button>
      </>}

      {this.state.modal ? <Modal text={this.state.modalText} visible={this.state.modal} toggleModal={this.toggleModal} /> : null}

    </div>
    )
  }
}

const DateInput = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  p {
    font-weight: bold;
    margin-right: 15px;
  }
`;

const YearsContainer = styled.div`
  display: flex;
  margin-top: 10px;
`;

const MonthsContainer = styled.div`
  display: flex;
  margin-top: 10px;
`;

const Form = styled.form`
  width: 800px;
  margin: 0 auto;
`;

const FromFirst = styled.div`
  width: 800px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  input[type='number'] {
    margin-left: 10px;
    width: 50px;
    text-align: center;
  }
`;

const ContractRow = styled.div`
  margin-bottom: 5px;
  display: flex;
  justify-content: space-between;
  align-items: center;

  p {
    font-size: 14px;
    margin: 0;
    margin-right: 10px;
  }

  input {
    width: 100px;
  }

  div {
    display: flex;
    align-items: center;
  }
`;

export default connect(
  state => ({
    testStore: state
  }),
  dispatch => ({
    getContracts: (contracts) => {
      dispatch({ type: 'FETCH_ALL_CONTRACTS', payload: contracts})
    }
  })
)(Bills);