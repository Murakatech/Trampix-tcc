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

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      attachCnpjMask();
      attachLinkedInValidation();
    });
  } else {
    attachCnpjMask();
    attachLinkedInValidation();
  }
})();