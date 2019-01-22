export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_PENI_BY_RENTER':
      return [...action.payload]
    default:
      return state
  }
}