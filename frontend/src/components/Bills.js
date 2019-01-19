import React, { Component } from 'react'
import { connect } from 'react-redux'
import $ from 'jquery'
import styled from 'styled-components'
import Loader from './Loader'

class Bills extends Component {
  constructor(props){
    super(props);
    this.state = {
      fromFirstValue: 1,
      today: this.setTodayDate(),
      loading: true
    }
  }

  componentDidMount() {
    fetch(`http://arenda.local/api/contract/read_all.php`)
      .then(response => response.json())
      .then(res => this.props.getContracts(res)) 
      .then(() => this.setState({ loading: false }))
  }

  handleChangeFromFirstValue = e => this.setState({ fromFirstValue: e.target.value }); 

  setTodayDate = () => {
    let date = new Date();
    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();
    if (day < 10) {
      day = '0' + day;
    }
    if (month < 10) {
      month = '0' + month;
    }
    return year + '-' + month + '-' + day;
  }

  createRadio(value) {
    return (
      <label className='container-radio'>{value}
        <input name='year' type='radio' value={value} />
        <span className='checkmark'></span>
      </label>
    )
  }

  handleCheck = e => {
    const makeEnabled = $(e.target).data('id');
    if ( $(e.target).prop('checked') ) {
      $(`input[name='period_sum'][data-id=${makeEnabled}]`).prop('disabled', false);
    } else {
      $(`input[name='period_sum'][data-id=${makeEnabled}]`).prop('disabled', true);
    }
  }


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

      {loading ? <Loader></Loader> : <>
      <DateInput>
        <p className='date-error'>Выберите дату:</p>
        <input type='date' name='date' value={this.state.today} id='date' onChange={e => this.setState({today: e.target.value})}/>
      </DateInput>
      <hr/>
      <Form id='vystavlenie-schetov' method='POST' action=''>
        <p className='error renter-error'><strong>Выберите один или более договор:</strong></p>      
          
        {this.props.testStore.contracts.map((contract, index) => {           
          return this.createCheckbox(contract.short_name, contract.contract_number, contract.summa, contract.contract_id, contract.renter_id, index) 
        })}

        <hr />
        <span className='error year-error'><strong>Выберите год:</strong></span>
        <YearsContainer>
          {this.createRadio('2016')}
          {this.createRadio('2017')}
          {this.createRadio('2018')}
          {this.createRadio('2019')}
          {this.createRadio('2020')}
        </YearsContainer>
        <hr />
        <span className='error month-error'><strong>Выберите месяц:</strong></span>
        <MonthsContainer>
          <span>
            {this.createRadio('Январь')}
            {this.createRadio('Февраль')}
            {this.createRadio('Март')}
          </span>
          <span>
            {this.createRadio('Апрель')}
            {this.createRadio('Май')}
            {this.createRadio('Июнь')}
          </span>
          <span>
            {this.createRadio('Июль')}
            {this.createRadio('Август')}
            {this.createRadio('Сентябрь')}                                  
          </span>
          <span>
            {this.createRadio('Октябрь')}
            {this.createRadio('Ноябрь')}
            {this.createRadio('Декабрь')}                 
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
  
      <button className='btn' type='submit'>Выставить счёт</button>
      </>}
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