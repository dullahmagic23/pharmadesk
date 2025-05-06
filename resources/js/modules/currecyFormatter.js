const currency = (value) => {
  return new Intl.NumberFormat('en-TZ', {
    style: 'currency',
    currency: 'TZS',
  }).format(value)
}

export default currency;