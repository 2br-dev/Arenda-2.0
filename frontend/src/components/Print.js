import React, { Component } from 'react'
import { connect } from 'react-redux'
import styled from 'styled-components'
import Loader from './Loader'
import Modal from './Modal'
import $ from 'jquery'
import Functions from '../functions/Functions'
import DeleteIcon from '../images/baseline_delete_forever_white_18dp.png'

class Print extends Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: true,
      selectedRenter: '',
      selectedMonth: '',
      selectedYear: '',
      modal: false,
      modalText: ''
    }
    this.openModal    = this.openModal.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
  }

  // получаем арендаторов
  componentDidMount() {
    fetch(`http://arenda.local/api/renters/read_all.php`)
      .then(response => response.json())
      .then(res => this.props.getRenters(res)) 
      .then(() => this.setState({ loading: false }))
  }

  // сабмит формы
  handleSubmit = e => {
    e.preventDefault();
    this.setState({ modal: false })
    const { selectedRenter, selectedMonth, selectedYear } = this.state;
    const self = this;
    const F = new Functions();

    // данные
    const data = {
      year:    selectedYear,
      month:   F.getNumberOfMonth(selectedMonth), // получаем номер месяца
      renter:  selectedRenter
    };

    // console.log(data);
    // если выбран год - делаем запрос
    if (data.year) {        
      $.ajax({
        type: "POST",
        url: "http://arenda.local/api/invoice/read.php",
        data: data,
        success: function(res){
          if (res.result === 1) {
            $('#pechat-schetov').trigger("reset"); // при успехе ресет формы
            self.setState({ selectedRenter: '', selectedMonth: '', selectedYear: ''}) // и стейта
            self.props.getInvoices(res.invoices); // отправляем в redux данные
            if(res.invoices.length === 0) {
              self.openModal('Нет счетов за выбранный период.'); 
            }
          } else {
            self.openModal('Что-то пошло не так.'); // если нет - говорим пользователю
          }
        },
        error: function(err) {
          console.log(err);
        }
      });
    } else {
      self.openModal('Пожалуйста, выберите год'); // если нет - подсказка пользователю
    }
  }

  handleDelete(e) {
  const self = this;
  const id = e.target.dataset.invoice;
  $.ajax({
    type: "POST",
    url: "http://arenda.local/api/invoice/delete.php",
    data: { invoice_id: e.target.dataset.invoice, contract: e.target.dataset.contract },
    success: function(res){
      console.log(res);
      self.props.filterInvoices(self.props.testStore.invoices.filter(inv => inv.id !== id ));
    },
    error: function(err) {
      console.log(err);
    }
  });    
  }

  // ф-я показывающая модальное окно
  openModal(text) {
    this.setState({ modal: true, modalText: text});
  }
  // передаём в Modal для переключения видимости
  toggleModal = () => this.setState({ modal: false, modalText: ''});

  // ставим в стейт выбранного арендатора, месяц и год
  selectRenter = e => this.setState({ selectedRenter: e.target.value });
  handleSelectMonth = e => this.setState({ selectedMonth: e.target.value })
  handleSelectYear = e => this.setState({ selectedYear: e.target.value })

  // создаем радио баттон
  createRadio(value, name) {
    return (
      <label className='container-radio'>{value}
        <input name={name} type='radio' value={value} 
          onClick={name === 'year' ? this.handleSelectYear : this.handleSelectMonth} />
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
    const { loading, selectedRenter } = this.state;
    const store = this.props.testStore;

    return (
    <section className='container' style={{ 'width':'800px', 'paddingTop': '100px' }}> 
      {loading ? <Loader/> : (<>
     
      <Select name='selected-renter' onChange={this.selectRenter}>
        <option selected disabled hidden value={selectedRenter}>Все арендаторы</option>
        {selectedRenter !== '' ? <option value='0'>Все арендаторы</option> : null}
        {store.renters.map((renter, i) => {  
          return (
            <option key={i} value={renter.full_name}>{renter.short_name}</option>
          )
        })}
      </Select>

      <hr />

      <form id='pechat-schetov' action='' method='post' onSubmit={this.handleSubmit} style={{ 'marginBottom': '50px' }}>
        <p>Выберите год:</p>
        <YearsContainer>
          {this.createRadio('2016', 'year')}
          {this.createRadio('2017', 'year')}
          {this.createRadio('2018', 'year')}
          {this.createRadio('2019', 'year')}
          {this.createRadio('2020', 'year')}      
        </YearsContainer>
        <hr />
        <p>Выберите месяц:</p>
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
        <input type='submit' value='Показать' className='btn' />
      </form>
      {store.invoices.map((inv, i) => { 
        return (
        <div key={i} style={{ 'marginBottom':'30px'}}>
          <RenterInfo>
            <div>
              <p style={{ 'margin':'0' }}><b>{inv.renter}</b></p>
              <p style={{ 'margin':'0' }}> № счёта: <b>{inv.invoice_number}</b>, на сумму: <b>{inv.summa}₽</b>, от: <b>{inv.invoice_date}</b></p>
            </div>
            <span onClick={this.handleDelete} className='btn' data-invoice={inv.id} data-contract={inv.contract_id}>удалить<img src={DeleteIcon} /></span>
          </RenterInfo>
          <hr />
          <div>
            {inv.summa > inv.discount 
          ? 
            <><LinkRow>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sch&pr=0&disc=0`} target='_blank' >Счет</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=akt&pr=0&disc=0`} target='_blank' >Акт</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sf&pr=0&disc=0`} target='_blank' >Счет-фактура</a>
            </LinkRow>
            <LinkRow>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sch&pr=1&disc=0`} target='_blank' >Счет+печать</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=akt&pr=1&disc=0`} target='_blank' >Акт+печать</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sf&pr=1&disc=0`} target='_blank' >Счет-фактура+печать</a>
            </LinkRow></>
          :
            <><LinkRow>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sch&pr=0&disc=1`} target='_blank' >Счет (со скидкой)</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=akt&pr=0&disc=1`} target='_blank' >Акт (со скидкой)</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sf&pr=0&disc=1`} target='_blank' >Счет-фактура (со скидкой)</a>
            </LinkRow>
            <LinkRow>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sch&pr=1&disc=1`} target='_blank' >Счет (со скидкой)+печать</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=akt&pr=1&disc=1`} target='_blank' >Акт (со скидкой)+печать</a>
              <a href={`/schet-pechatnaya-forma?num=${inv.invoice_number}&ind=sf&pr=1&disc=1`} target='_blank' >Счет-фактура (со скидкой)+печать</a>
            </LinkRow></>
          }
          </div>  
        </div>
        )
      })}


      </>)}

      {this.state.modal ? <Modal text={this.state.modalText} visible={this.state.modal} toggleModal={this.toggleModal} /> : null}
      </section>
    )
  }
}

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

const YearsContainer = styled.div`
  display: flex;
  margin-top: 10px;
`;

const MonthsContainer = styled.div`
  display: flex;
  margin-top: 10px;
`;

const Select = styled.select`
  color: #fff;
  background: #232323;
  border: 0;
  padding: 10px 20px;
  border-radius: 10px;
  margin-bottom: 15px;
  width: 300px;
  position: absolute;
  left: 0;
  right: 0;
  margin: auto;
  top: 50px;
`;

const RenterInfo = styled.div`
  display: flex;
  align-items: center;
  justify-content: space-between;

  span {
    display: flex;
    align-items: center;
    img {
      margin-left: 10px;
    }
  }

  .btn {
    text-transform: capitalize;
    margin: unset;
    padding: 7px 25px 10px;
  }
`;

const LinkRow = styled.div`
  a {
    margin-right: 25px;
  }
`;

export default connect(
  state => ({
    testStore: state
  }),
  dispatch => ({
    getInvoices: (invoices) => {
      dispatch({ type: 'FETCH_ALL_INVOICES', payload: invoices})
    },
    getRenters: (renters) => {
      dispatch({ type: 'FETCH_ALL_RENTERS', payload: renters})
    },
    filterInvoices: (filtered) => {
      dispatch({ type: 'DELETE_INVOICE', payload: filtered})
    }
  })
)(Print);