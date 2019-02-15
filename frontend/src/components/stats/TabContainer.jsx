import React from 'react';
import _ from 'lodash';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import Functions from '../../functions/Functions';
import MenuItem from '@material-ui/core/MenuItem';
import Select from '@material-ui/core/Select';
import InputLabel from '@material-ui/core/InputLabel';
import FormControl from '@material-ui/core/FormControl';
import selectData from './selectData';
import styled from 'styled-components';
import Fab from '@material-ui/core/Fab';
import ClearIcon from '@material-ui/icons/Clear';
import DeleteIcon from '@material-ui/icons/Delete';
import Tooltip from '@material-ui/core/Tooltip';
import Loader from '../Loader';
import $ from 'jquery';

const CustomTableCell = withStyles(theme => ({
  head: {
    backgroundColor: theme.palette.common.black,
    color: theme.palette.common.white,
  },
  body: {
    fontSize: 16
  },
}))(TableCell);

const styles = theme => ({
  root: {
    width: '100%',
    overflowX: 'auto',
    margin: '0 auto'
  },
  table: {
    minWidth: 500,
  },
  row: {
    '&:nth-of-type(odd)': {
      backgroundColor: theme.palette.background.default,
    },
  },
  absolute: {
    position: 'fixed',
    bottom: theme.spacing.unit * 2,
    right: theme.spacing.unit * 3,
  },
  success: { color: 'green' },
  warning: { color: 'red' },
  bold: { fontWeight: 'bold' }
});


class TabContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      services: [],
      type: '',
      month: '',
      year: '',
      allTypes: selectData.types,
      allYears: selectData.years,
      allMonths: selectData.months,
      loading: true
    }
    this.handleChange = this.handleChange.bind(this);
    this.fetchServices = this.fetchServices.bind(this);
  }

  componentDidMount = () => this.fetchServices();

  async handleChange(e) {
    await this.setState({loading: true})
    await this.fetchServices();
    await setTimeout(() => this.filterServices(e), 0); 
    await this.setState({loading: false})
  }

  async fetchServices() {
    await fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/communal/read.php`)
      .then(response => response.json())
      .then(services => this.setState({ services }))
      .catch(err => console.log(err))
    
    await this.setState({ loading: false })  
  }

  filterServices = (e) => {
    this.setState({ [e.target.name]: e.target.value });
    const filtered = this.state.services;
    
    if (e.target.value === '') {
      this.fetchServices();
    } else {
      let services = filtered.filter(service => service[e.target.name] == e.target.value);
      this.setState({ services });
    }   
  }

  clearFilters = () => {
    this.setState({type: '', year: '', month: '', });
    this.fetchServices();
  }

  async handleDelete(id) {
    const self = this;
    this.setState({ loading: true })

    await $.ajax({ 
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/communal/delete.php`,
      type: 'POST',
      data: { id },
      success: function(res) {
        console.log(res);
      },
      error: function(err) {
        console.log(err);
      }
    });
          
    await (function() {
      self.fetchServices();
    })();
  }

  render() {
    const { classes } = this.props;
    const { services, allTypes, allMonths, allYears } = this.state;
    const F = new Functions();

    return (
      <Paper className={classes.root}>
        { !this.state.loading ? (
        <Table className={classes.table}>
          
          <TableHead className={classes.row}>
            <TableRow>
              <CustomTableCell>
                <Wrapper>
                <FormControl style={{ width:'100%' }}>
                  <InputLabel htmlFor="type" style={{ color:'white' }}>Тип услуги</InputLabel>
                  <Select
                    style={{ color:'white', textAlign: 'left' }}
                    value={this.state.type}
                    onChange={this.handleChange}
                    inputProps={{
                      name: 'type',
                      id: 'type',
                    }}
                  >
                  <MenuItem value=''>Все типы</MenuItem>
                  {allTypes.map((item, i) => (
                    <MenuItem key={i} value={item.value}>{ item.label }</MenuItem>
                  ))}
                  </Select>
                </FormControl>
                </Wrapper>
              </CustomTableCell>
              <CustomTableCell align="right">
                <Wrapper>
                <FormControl style={{ width:'100%' }}>
                  <InputLabel htmlFor="month" style={{ color:'white' }}>Месяц</InputLabel>
                  <Select
                    style={{ color:'#fafafa', textAlign: 'left'}}
                    value={this.state.month}
                    onChange={this.handleChange}
                    inputProps={{
                      name: 'month',
                      id: 'month',
                    }}
                  >
                  <MenuItem value=''>Все месяца</MenuItem>
                  {allMonths.map((item, i) => (
                    <MenuItem key={i} value={item.value}>{ item.label }</MenuItem>
                  ))}
                  </Select>
                </FormControl>
                </Wrapper>
              </CustomTableCell>
              <CustomTableCell align="right">
                <Wrapper>
                <FormControl style={{ width:'100%' }}>
                  <InputLabel htmlFor="year" style={{ color:'white' }}>Год</InputLabel>
                  <Select
                    style={{ color:'white', textAlign: 'left' }}
                    value={this.state.year}
                    onChange={this.handleChange}
                    inputProps={{
                      name: 'year',
                      id: 'year',
                    }}
                  >
                  <MenuItem value=''>Все года</MenuItem>
                  {allYears.map((item, i) => (
                    <MenuItem key={i} value={item.value}>{ item.label }</MenuItem>
                  ))}
                  </Select>
                </FormControl>
                </Wrapper>
              </CustomTableCell>
              <CustomTableCell align="right">Сумма</CustomTableCell>
              <CustomTableCell align="right">Количество</CustomTableCell>
              <CustomTableCell align="center"></CustomTableCell>
            </TableRow> 
             
          </TableHead>
          
          <TableBody>
            {services.map(service => (
              <TableRow className={classes.row} key={service.id}>
                <CustomTableCell component="th" scope="row" className={classes.bold}>
                  {service.type}
                </CustomTableCell>
                <CustomTableCell align="right">{F.getStringOfMonth(service.month)}</CustomTableCell>
                <CustomTableCell align="right">{service.year}</CustomTableCell>  
                <CustomTableCell align="right">{F.toPrice(service.summa)}</CustomTableCell>
                <CustomTableCell align="right">{F.toPrice(service.amount)}</CustomTableCell>
                <CustomTableCell align="center">
                  <Tooltip title="Удалить" aria-label="Удалить"><Icon><DeleteIcon onClick={() => this.handleDelete(service.id)}/></Icon></Tooltip>
                </CustomTableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
        
        ) : <Loader />}
        <Tooltip title="Очистить фильтры" aria-label="Очистить фильтры">
          <Fab color="primary" className={classes.absolute} onClick={this.clearFilters}>
            <ClearIcon />
          </Fab>
        </Tooltip>
      </Paper>
    );
  }
}
const Icon = styled.div`
  cursor: pointer;
  opacity: .85;

  &:hover {
    opacity: 1;
    transition: .37s ease;
  }
`
const Wrapper = styled.div`
  svg {
    color: #fafafa !important;
  }
`
TabContainer.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(TabContainer);