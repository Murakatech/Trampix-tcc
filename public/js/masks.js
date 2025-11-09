// Robust client-side helpers for CNPJ mask and LinkedIn URL validation (caret-aware)
(function () {
  function onlyDigits(str) {
    return (str || '').replace(/\D+/g, '');
  }

  function formatCnpjFromDigits(digits) {
    const d = (digits || '').replace(/\D+/g, '').slice(0, 14);
    let out = '';
    if (d.length <= 2) return d;
    out = d.slice(0, 2) + '.';
    if (d.length <= 5) return out + d.slice(2);
    out += d.slice(2, 5) + '.';
    if (d.length <= 8) return out + d.slice(5);
    out += d.slice(5, 8) + '/';
    if (d.length <= 12) return out + d.slice(8);
    out += d.slice(8, 12) + '-';
    return out + d.slice(12);
  }

  function formatCnpj(value) {
    return formatCnpjFromDigits(onlyDigits(value));
  }

  // Map caret from number of digits before caret to new index in formatted string
  function mapDigitsPosToFormattedIndex(formatted, digitsPos) {
    if (digitsPos <= 0) return 0;
    let count = 0;
    for (let i = 0; i < formatted.length; i++) {
      if (/\d/.test(formatted[i])) {
        count++;
        if (count === digitsPos) return i + 1; // caret after this digit
      }
    }
    return formatted.length; // end
  }

  function handleCnpjInput(e) {
    const input = e.target;
    const raw = input.value;
    const caret = input.selectionStart || raw.length;
    const digitsBeforeCaret = onlyDigits(raw.slice(0, caret)).length;
    const digitsAll = onlyDigits(raw).slice(0, 14);
    const formatted = formatCnpjFromDigits(digitsAll);
    input.value = formatted;
    try {
      const newCaret = mapDigitsPosToFormattedIndex(formatted, digitsBeforeCaret);
      input.setSelectionRange(newCaret, newCaret);
    } catch (_) {}
  }

  function handleCnpjPaste(e) {
    const input = e.target;
    const text = (e.clipboardData || window.clipboardData).getData('text');
    if (!text) return;
    e.preventDefault();
    const digits = onlyDigits(text).slice(0, 14);
    const formatted = formatCnpjFromDigits(digits);
    const start = input.selectionStart || 0;
    const end = input.selectionEnd || 0;
    const current = input.value;
    // Replace selected range
    const before = current.slice(0, start);
    const after = current.slice(end);
    const mergedDigits = onlyDigits(before + formatted + after).slice(0, 14);
    const final = formatCnpjFromDigits(mergedDigits);
    input.value = final;
    try {
      const newCaret = mapDigitsPosToFormattedIndex(final, onlyDigits(before + formatted).length);
      input.setSelectionRange(newCaret, newCaret);
    } catch (_) {}
  }

  function attachCnpjMask() {
    // Support both class and data-mask attributes
    const byClass = Array.from(document.querySelectorAll('input.br-cnpj'));
    const byData = Array.from(document.querySelectorAll('input[data-mask="br-cnpj"]'));
    const inputs = Array.from(new Set([...byClass, ...byData]));
    inputs.forEach((input) => {
      input.setAttribute('inputmode', 'numeric');
      if (!input.placeholder) input.placeholder = '00.000.000/0000-00';
      // Initialize once to formatted form
      input.value = formatCnpj(input.value);
      input.addEventListener('input', handleCnpjInput);
      input.addEventListener('paste', handleCnpjPaste);
    });
  }

  function isValidLinkedIn(url) {
    if (!url) return true; // allow empty when field is optional
    try {
      const u = new URL(url);
      return /(^|\.)linkedin\.com$/.test(u.hostname);
    } catch (e) {
      return false;
    }
  }

  function attachLinkedInValidation() {
    const inputs = document.querySelectorAll('input[name="linkedin_url"]');
    inputs.forEach((input) => {
      input.addEventListener('blur', () => {
        const val = input.value.trim();
        if (!val) {
          input.setCustomValidity('');
          return;
        }
        if (!isValidLinkedIn(val)) {
          input.setCustomValidity('Por favor, informe uma URL válida do LinkedIn.');
        } else {
          input.setCustomValidity('');
        }
      });
      input.addEventListener('input', () => input.setCustomValidity(''));
    });
  }

  // BR Phone mask: (99) 99999-9999 or (99) 9999-9999 as user types
  function formatBrPhone(value) {
    const digits = (value || '').replace(/\D/g, '').slice(0, 11);
    if (digits.length === 0) return '';
    if (digits.length <= 2) return `(${digits}`;
    const ddd = digits.slice(0, 2);
    const after = digits.slice(2);
    if (after.length <= 4) return `(${ddd}) ${after}`;
    if (digits.length <= 10) return `(${ddd}) ${digits.slice(2, 6)}-${digits.slice(6, 10)}`;
    return `(${ddd}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
  }

  function handlePhoneInput(e) {
    const el = e.target;
    el.value = formatBrPhone(el.value);
    const len = el.value.length;
    try { el.setSelectionRange(len, len); } catch(_) {}
  }

  function attachPhoneMask() {
    const inputs = document.querySelectorAll('input[data-mask="br-phone"]');
    inputs.forEach((input) => {
      input.setAttribute('inputmode', 'numeric');
      input.addEventListener('input', handlePhoneInput);
      if (input.value) input.value = formatBrPhone(input.value);
    });
  }

  // BR Currency mask: R$ 1.234,56 (type=text only)
  function formatBRL(value) {
    const prefix = 'R$ ';
    if (!value) return '';
    const raw = String(value).replace(/^R\$\s*/, '');
    const parts = raw.split(',');
    let integer = (parts[0] || '').replace(/\D/g, '');
    let decimals = (parts[1] || '').replace(/\D/g, '').slice(0, 2);
    if (!integer) return '';
    const integerFormatted = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return prefix + integerFormatted + (decimals ? ',' + decimals : '');
  }

  function handleCurrencyInput(e) {
    const el = e.target;
    el.value = formatBRL(el.value);
    const len = el.value.length;
    try { el.setSelectionRange(len, len); } catch(_) {}
  }

  function attachCurrencyMask() {
    const inputs = document.querySelectorAll('input[data-mask="br-currency"]');
    inputs.forEach((input) => {
      input.addEventListener('input', handleCurrencyInput);
      if (input.value) input.value = formatBRL(input.value);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      attachCnpjMask();
      attachLinkedInValidation();
      attachPhoneMask();
      attachCurrencyMask();
    });
  } else {
    attachCnpjMask();
    attachLinkedInValidation();
    attachPhoneMask();
    attachCurrencyMask();
  }
})();