(function(){
  function fetchList() {
    return fetch('php_action/branches.php?action=list').then(function(r){return r.json();});
  }
  function renderTable(items) {
    var tbody = document.querySelector('#branchAccountsTable tbody');
    if (!tbody) { console.warn('branchAccountsTable tbody not found'); return; }
    tbody.innerHTML = '';
    (items || []).forEach(function(it){
      var tr = document.createElement('tr');
      var hasPerson = !!(it.contact_person && String(it.contact_person).trim());
      var hasPhone = !!(it.contact_phone && String(it.contact_phone).trim());
      if (!hasPerson || !hasPhone) { tr.classList.add('table-warning'); }

      // Branch cell
      var tdBranch = document.createElement('td');
      tdBranch.textContent = it.name || '';
      if (!hasPerson || !hasPhone) {
        var warn = document.createElement('span');
        warn.className = 'ml-1';
        warn.setAttribute('data-toggle', 'tooltip');
        warn.setAttribute('title', 'Missing contact info');
        warn.innerHTML = '<i class="fa fa-exclamation-triangle text-warning" aria-hidden="true"></i>';
        tdBranch.appendChild(warn);
      }
      tr.appendChild(tdBranch);

      // Person cell
      var tdPerson = document.createElement('td');
      tdPerson.innerHTML = hasPerson ? (it.contact_person || '') : '<span class="text-muted">&mdash;</span>';
      tr.appendChild(tdPerson);

      // Phone cell
      var tdPhone = document.createElement('td');
      tdPhone.innerHTML = hasPhone ? (it.contact_phone || '') : '<span class="text-muted">&mdash;</span>';
      tr.appendChild(tdPhone);

      // Created at
      var tdCreated = document.createElement('td');
      tdCreated.textContent = it.created_at || '';
      tr.appendChild(tdCreated);

      // Action
      var tdAction = document.createElement('td');
      tdAction.className = 'text-center';
      var group = document.createElement('div');
      group.className = 'btn-group btn-group-sm';
      group.setAttribute('role', 'group');

      var editBtn = document.createElement('button');
      editBtn.type = 'button';
      editBtn.className = 'btn btn-outline-primary';
      editBtn.setAttribute('data-action', 'edit');
      editBtn.setAttribute('data-id', it.pharmacy_id || '');
      editBtn.setAttribute('title', 'Edit');
      editBtn.setAttribute('aria-label', 'Edit');
      editBtn.setAttribute('data-toggle', 'tooltip');
      editBtn.setAttribute('data-placement', 'top');
      editBtn.innerHTML = '<i class="fa fa-edit" aria-hidden="true"></i> <span class="d-none d-sm-inline">Edit</span>';

      var delBtn = document.createElement('button');
      delBtn.type = 'button';
      delBtn.className = 'btn btn-outline-danger';
      delBtn.setAttribute('data-action', 'delete');
      delBtn.setAttribute('data-id', it.pharmacy_id || '');
      delBtn.setAttribute('title', 'Delete');
      delBtn.setAttribute('aria-label', 'Delete');
      delBtn.setAttribute('data-toggle', 'tooltip');
      delBtn.setAttribute('data-placement', 'top');
      delBtn.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> <span class="d-none d-sm-inline">Delete</span>';

      group.appendChild(editBtn);
      group.appendChild(delBtn);
      tdAction.appendChild(group);
      tr.appendChild(tdAction);

      tbody.appendChild(tr);
    });

    // initialize tooltips on newly added buttons
    if (typeof $ === 'function' && typeof $.fn.tooltip === 'function') {
      $('[data-toggle="tooltip"]').tooltip();
    }
    console.log('Rendered branches rows:', (items||[]).length);
  }
  function populateSelect(items) {
    var sel = document.getElementById('branch_pharmacy_select');
    if (!sel) return;
    sel.innerHTML = '';
    items.forEach(function(it){
      var opt = document.createElement('option');
      opt.value = it.pharmacy_id;
      opt.textContent = it.name;
      sel.appendChild(opt);
    });
  }
  function updateBranch(payload) {
    var fd = new FormData();
    Object.keys(payload).forEach(function(k){ fd.append(k, payload[k]); });
    return fetch('php_action/branches.php?action=update', { method: 'POST', body: fd }).then(function(r){return r.json();});
  }
  function deleteBranch(pharmacyId) {
    var fd = new FormData();
    fd.append('pharmacy_id', pharmacyId);
    return fetch('php_action/branches.php?action=delete', { method: 'POST', body: fd }).then(function(r){return r.json();});
  }

  document.addEventListener('DOMContentLoaded', function(){
    var saveBtn = document.getElementById('saveBranchContact');
    if (!saveBtn) return;
    var msg = document.getElementById('branchMsg');
    var tbody = document.querySelector('#branchAccountsTable tbody');
    // init phone input with Rwanda flag
    var phoneInput = document.getElementById('branch_contact_phone');
    var phoneHidden = document.getElementById('branch_contact_phone_e164');
    var iti = null;
    if (phoneInput && window.intlTelInput) {
      iti = window.intlTelInput(phoneInput, {
        initialCountry: 'rw',
        onlyCountries: ['rw'],
        separateDialCode: true,
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
      });
    }
    fetchList().then(function(data){
      var items = data.items || [];
      populateSelect(items);
      renderTable(items);
    });

    // delegated events for action buttons
    if (tbody) {
      tbody.addEventListener('click', function(e){
        var btn = e.target.closest('button[data-action]');
        if (!btn) return;
        var action = btn.getAttribute('data-action');
        var id = btn.getAttribute('data-id');
        var row = btn.closest('tr');
        if (action === 'edit') {
          var person = row.children[1] ? row.children[1].textContent : '';
          var phone = row.children[2] ? row.children[2].textContent : '';
          var sel = document.getElementById('branch_pharmacy_select');
          if (sel) sel.value = id;
          var personInput = document.getElementById('branch_contact_person');
          var phoneInput = document.getElementById('branch_contact_phone');
          if (personInput) personInput.value = person || '';
          if (phoneInput) phoneInput.value = phone || '';
          if (typeof window.scrollTo === 'function') {
            var anchor = document.querySelector('#branch_pharmacy_select');
            if (anchor) window.scrollTo({ top: anchor.getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
          }
        } else if (action === 'delete') {
          if (!id) return;
          if (!confirm('Delete this branch permanently?')) return;
          deleteBranch(id)
            .then(function(resp){
              if (!resp || !resp.success) throw new Error('Delete failed');
            })
            .then(function(){ return fetchList(); })
            .then(function(data){ renderTable(data.items || []); })
            .catch(function(){ alert('Failed to delete branch.'); });
        }
      });
    }
    function showMsg(type, text) {
      if (!msg) return;
      msg.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        text +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
          '<span aria-hidden="true">&times;</span>' +
        '</button>' +
      '</div>';
    }

    function isValidName(s){ return (s||'').trim().length >= 3; }
    function isValidPhoneRaw(s){ return (s||'').trim().length >= 8; }

    saveBtn.addEventListener('click', function(){
      var id = document.getElementById('branch_pharmacy_select').value;
      var person = document.getElementById('branch_contact_person').value;
      var phone = document.getElementById('branch_contact_phone').value;
      if (iti && phoneHidden) {
        phoneHidden.value = iti.getNumber();
      }

      // validations
      if (!id) { showMsg('warning', 'Please select a branch pharmacy.'); return; }
      if (!isValidName(person)) { showMsg('warning', 'Please enter a valid contact person (min 3 chars).'); return; }
      var phoneToSend = phoneHidden && phoneHidden.value ? phoneHidden.value : phone;
      if (!(phoneHidden && phoneHidden.value) && !isValidPhoneRaw(phone)) {
        showMsg('warning', 'Please enter a valid contact phone.'); return; }

      // disable while saving
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

      updateBranch({ pharmacy_id: id, contact_person: person.trim(), contact_phone: phoneToSend })
        .then(function(resp){
          if (resp && resp.success) { showMsg('success', 'Branch contact saved successfully.'); }
          else { showMsg('danger', 'Failed to save branch contact.'); }
          return fetchList();
        })
        .then(function(data){ renderTable(data.items || []); })
        .catch(function(){ showMsg('danger', 'Network error. Please try again.'); })
        .finally(function(){
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i class="fa fa-save"></i> Save';
        });
    });
  });
})();


