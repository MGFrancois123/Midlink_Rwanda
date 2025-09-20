<?php 
require_once('./constant/connect.php');
?>

<div class="left-sidebar" style="background:#0f172a;">
    
    <div class="scroll-sidebar">
        
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-devider"></li>
                <li class="nav-label">Home</li>
                
                <?php if(isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'super_admin') { ?>
                <li>
                  <a href="dashboard_super.php" aria-expanded="false"><i class="fa fa-tachometer"></i> <span>Super Admin Dashboard</span></a>
                </li>
                <?php } ?>
                
                <?php if(isset($_SESSION['userRole']) && $_SESSION['userRole']==='super_admin') { ?>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-users"></i><span class="hide-menu">User Management</span></a>
                    <ul aria-expanded="false" class="collapse">
                      <li><a href="add_user.php"><i class="fa fa-user-plus"></i> <span>Add User</span></a></li>
                      <li><a href="create_pharmacy.php"><i class="fa fa-hospital-o"></i> <span>Create Pharmacy</span></a></li>
                      <li><a href="manage_pharmacies.php"><i class="fa fa-building"></i> <span>Manage Pharmacy</span></a></li>
                      <li><a href="medical_staff.php"><i class="fa fa-user-md"></i> <span>Medical Staff</span></a></li>
                      <li><a href="placeholder.php?title=User%20Activity"><i class="fa fa-history"></i> <span>User Activity</span></a></li>
                    </ul>
                </li>
                
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-medkit"></i><span class="hide-menu">Medicine Catalog</span></a>
                    <ul aria-expanded="false" class="collapse">
                      <li><a href="add-product.php"><i class="fa fa-pencil-square-o"></i> <span>Add / Update Medicines</span></a></li>
                      <li><a href="categories.php"><i class="fa fa-tags"></i> <span>Categories</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-shield"></i><span class="hide-menu">Audit & Compliance</span></a>
                    <ul aria-expanded="false" class="collapse">
                      <li><a href="audit_logs.php"><i class="fa fa-list-alt"></i> <span>Audit Logs</span></a></li>
                      <li><a href="security_logs.php"><i class="fa fa-shield"></i> <span>Security Logs</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-credit-card"></i><span class="hide-menu">Payments & Billing</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="all_transactions.php"><i class="fa fa-list"></i> <span>All Transactions</span></a></li>
                        <li><a href="refund_requests.php"><i class="fa fa-undo"></i> <span>Refund Requests</span></a></li>
                        <li><a href="failed_payments.php"><i class="fa fa-times-circle"></i> <span>Failed Payments</span></a></li>
                        <li><a href="daily_revenue.php"><i class="fa fa-money"></i> <span>Daily Revenue</span></a></li>
                        <li><a href="weekly_monthly_revenue.php"><i class="fa fa-calendar"></i> <span>Weekly / Monthly Revenue</span></a></li>
                        <li><a href="outstanding_balances.php"><i class="fa fa-balance-scale"></i> <span>Outstanding Balances</span></a></li>
                        <li><a href="branch_reconciliation.php"><i class="fa fa-random"></i> <span>Branch Reconciliation</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-file-text"></i><span class="hide-menu">Reports & Analytics</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="revenue_reports.php"><i class="fa fa-area-chart"></i> <span>Revenue Reports</span></a></li>
                        <li><a href="transaction_exceptions.php"><i class="fa fa-exclamation-circle"></i> <span>Transaction Exceptions</span></a></li>
                        <li><a href="suspicious_transactions.php"><i class="fa fa-eye"></i> <span>Suspicious Transactions</span></a></li>
                    </ul>
                </li>
                
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive"></i><span class="hide-menu">Inventory Management</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="product.php"><i class="fa fa-list-ul"></i> <span>All Medicines</span></a></li>
                        <li><a href="stock_movements.php"><i class="fa fa-exchange"></i> <span>Stock (IN / OUT)</span></a></li>
                        <li><a href="low_stock_alerts.php"><i class="fa fa-level-down"></i> <span>Low Stock Alerts</span></a></li>
                        <li><a href="expiry_alerts.php"><i class="fa fa-clock-o"></i> <span>Expiry Alerts</span></a></li>
                    </ul>
                </li>
                
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bell"></i><span class="hide-menu">Notifications</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="send_sms.php"><i class="fa fa-envelope"></i> <span>Send SMS</span></a></li>
                        <li><a href="pickup_reminders.php"><i class="fa fa-bell-o"></i> <span>Pickup Reminders</span></a></li>
                        <li><a href="recall_alerts.php"><i class="fa fa-exclamation-triangle"></i> <span>Recall Alerts</span></a></li>
                    </ul>
                </li>
                <?php } ?>

                <?php if(isset($_SESSION['userRole']) && $_SESSION['userRole']==='finance_admin') { ?>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-credit-card"></i><span class="hide-menu">Payments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=All%20Transactions"><i class="fa fa-list"></i> <span>All Transactions</span></a></li>
                        <li><a href="placeholder.php?title=Refund%20Requests"><i class="fa fa-undo"></i> <span>Refund Requests</span></a></li>
                        <li><a href="placeholder.php?title=Failed%20Payments"><i class="fa fa-times-circle"></i> <span>Failed Payments</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-calculator"></i><span class="hide-menu">Billing & Accounting</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Daily%20Revenue"><i class="fa fa-money"></i> <span>Daily Revenue</span></a></li>
                        <li><a href="placeholder.php?title=Weekly%20%2F%20Monthly%20Revenue"><i class="fa fa-calendar"></i> <span>Weekly / Monthly Revenue</span></a></li>
                        <li><a href="placeholder.php?title=Outstanding%20Balances"><i class="fa fa-balance-scale"></i> <span>Outstanding Balances</span></a></li>
                        <li><a href="placeholder.php?title=Branch%20Reconciliation"><i class="fa fa-random"></i> <span>Branch Reconciliation</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-file-text"></i><span class="hide-menu">Reports</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Revenue%20Reports"><i class="fa fa-area-chart"></i> <span>Revenue Reports</span></a></li>
                        <li><a href="placeholder.php?title=Transaction%20Exceptions"><i class="fa fa-exclamation-circle"></i> <span>Transaction Exceptions</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-exclamation-triangle"></i><span class="hide-menu">Alerts</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Suspicious%20Transactions"><i class="fa fa-eye"></i> <span>Suspicious Transactions</span></a></li>
                        <li><a href="placeholder.php?title=Payment%20Failures"><i class="fa fa-times-circle"></i> <span>Payment Failures</span></a></li>
                    </ul>
                </li>
                <?php }?>

                <?php if(isset($_SESSION['userRole']) && $_SESSION['userRole']==='user') { ?>
                <!-- Medicine Store Section - Always visible for users -->
                <br><br><br>
                <li><a href="store.php"><i class="fa fa-store"></i> Medicine Store</a></li>
                <li><a href="cart.php"><i class="fa fa-shopping-cart"></i> Shopping Cart</a></li>
                <li><a href="order_history.php"><i class="fa fa-history"></i> Order History</a></li>
                
                <!-- Additional user sections -->
                <!-- <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-user"></i><span class="hide-menu">My Profile</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Personal%20Info"><i class="fa fa-id-badge"></i> <span>Personal Info</span></a></li>
                        <li><a href="placeholder.php?title=Medical%20History"><i class="fa fa-heartbeat"></i> <span>Medical History</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-calendar"></i><span class="hide-menu">Appointments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Book%20Appointment"><i class="fa fa-calendar-plus-o"></i> <span>Book Appointment</span></a></li>
                        <li><a href="placeholder.php?title=View%20Appointments"><i class="fa fa-calendar"></i> <span>View Appointments</span></a></li>
                        <li><a href="placeholder.php?title=Cancel%20Appointment"><i class="fa fa-calendar-times-o"></i> <span>Cancel Appointment</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-file-text"></i><span class="hide-menu">Prescriptions</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=View%20Prescription"><i class="fa fa-file-text-o"></i> <span>View Prescription</span></a></li>
                        <li><a href="placeholder.php?title=Linked%20Medicines"><i class="fa fa-link"></i> <span>Linked Medicines</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-credit-card"></i><span class="hide-menu">Payments</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Pay%20with%20Mobile%20Money%20%2F%20Card"><i class="fa fa-credit-card"></i> <span>Pay with Mobile Money / Card</span></a></li>
                        <li><a href="placeholder.php?title=Payment%20History"><i class="fa fa-history"></i> <span>Payment History</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bell"></i><span class="hide-menu">Notifications</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=SMS%20Reminders"><i class="fa fa-commenting-o"></i> <span>SMS Reminders</span></a></li>
                        <li><a href="placeholder.php?title=Alerts%20%26%20Messages"><i class="fa fa-bell"></i> <span>Alerts & Messages</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-android"></i><span class="hide-menu">AI Health Support</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Symptom%20Checker"><i class="fa fa-medkit"></i> <span>Symptom Checker</span></a></li>
                        <li><a href="placeholder.php?title=Prediction%20History"><i class="fa fa-history"></i> <span>Prediction History</span></a></li>
                    </ul>
                </li> -->
                <?php }?>

                <?php if(isset($_SESSION['userRole']) && $_SESSION['userRole']==='regulator') { ?>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-tachometer"></i><span class="hide-menu">Dashboards</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Controlled%20Drug%20Utilization"><i class="fa fa-bar-chart"></i> <span>Controlled Drug Utilization</span></a></li>
                        <li><a href="placeholder.php?title=Outlier%20Pharmacies%20%2F%20Prescribers"><i class="fa fa-exclamation-triangle"></i> <span>Outlier Pharmacies / Prescribers</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-check"></i><span class="hide-menu">Compliance</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=Audit%20Trail%20Access"><i class="fa fa-list-alt"></i> <span>Audit Trail Access</span></a></li>
                        <li><a href="placeholder.php?title=Stock%20Reconciliation%20Reports"><i class="fa fa-exchange"></i> <span>Stock Reconciliation Reports</span></a></li>
                        <li><a href="placeholder.php?title=Expiry%20%26%20Recall%20Tracking"><i class="fa fa-clock-o"></i> <span>Expiry & Recall Tracking</span></a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-file"></i><span class="hide-menu">Reports</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="placeholder.php?title=National%20Usage%20Reports"><i class="fa fa-area-chart"></i> <span>National Usage Reports</span></a></li>
                        <li><a href="placeholder.php?title=ADR%20%26%20Recall%20Outcomes"><i class="fa fa-ambulance"></i> <span>ADR & Recall Outcomes</span></a></li>
                    </ul>
                </li>
                <?php }?>

            </ul>   
        </nav>
        
    </div>
    
</div>
<script>
(function() {
    var sidebar = document.getElementById('sidebarnav');
    if (!sidebar) { return; }

    // Function to hide all submenus
    function hideAllSubmenus() {
        var allSubmenus = sidebar.querySelectorAll('ul.collapse');
        for (var s = 0; s < allSubmenus.length; s++) {
            allSubmenus[s].classList.remove('show');
            var parentLi = allSubmenus[s].closest('li');
            if (parentLi) {
                parentLi.classList.remove('open');
                var trigger = parentLi.querySelector('a.has-arrow');
                if (trigger) { trigger.setAttribute('aria-expanded', 'false'); }
            }
        }
    }

    // Collapse all submenus by default
    hideAllSubmenus();

    // Toggle submenus on click for items with .has-arrow
    var toggles = sidebar.querySelectorAll('a.has-arrow');
    for (var i = 0; i < toggles.length; i++) {
        toggles[i].addEventListener('click', function(e) {
            e.preventDefault();
            var parentLi = this.parentElement;
            var subMenu = this.nextElementSibling;
            if (subMenu && subMenu.tagName === 'UL') {
                var isOpen = subMenu.classList.contains('show');
                
                // Close ALL other submenus first
                hideAllSubmenus();
                
                // Now toggle the clicked submenu
                if (!isOpen) {
                    subMenu.classList.add('show');
                    parentLi.classList.add('open');
                    this.setAttribute('aria-expanded', 'true');
                    try { parentLi.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } catch(_) {}
                } else {
                    subMenu.classList.remove('show');
                    parentLi.classList.remove('open');
                    this.setAttribute('aria-expanded', 'false');
                }
                // Persist last opened menu to avoid jumping back unexpectedly
                try { sessionStorage.setItem('mdlink_last_menu', this.textContent.trim()); } catch(_) {}
            }
        });
    }

    // Highlight current link and open its parents (match including querystring)
    var currentPath = window.location.pathname.split('/').pop();
    var currentQuery = window.location.search || '';
    var currentUrl = (currentPath || 'index.php') + currentQuery;
    var links = sidebar.querySelectorAll('a[href]');
    var activeSubmenu = null;
    for (var k = 0; k < links.length; k++) {
        var href = links[k].getAttribute('href');
        if (!href || href === '#' || href.indexOf('javascript:') === 0) { continue; }
        // Normalize href: handle absolute URLs by extracting last segment + query
        var file = href.split('#')[0];
        try {
          var a = document.createElement('a');
          a.href = file;
          var last = (a.pathname || '').split('/').pop();
          var q = a.search || '';
          file = (last || file) + q;
        } catch(_) {}
        if (file === currentPath || file === currentUrl) {
            links[k].classList.add('active');
            var p = links[k].parentElement;
            while (p && p !== sidebar) {
                if (p.tagName === 'UL' && p.classList.contains('collapse')) {
                    p.classList.add('show');
                    activeSubmenu = p;
                }
                p = p.parentElement;
            }
        }
    }
    
    // Close all other submenus except the active one
    if (activeSubmenu) {
        var allSubmenus = sidebar.querySelectorAll('ul.collapse');
        for (var s = 0; s < allSubmenus.length; s++) {
            if (allSubmenus[s] !== activeSubmenu) {
                allSubmenus[s].classList.remove('show');
                var parentLi = allSubmenus[s].closest('li');
                if (parentLi) {
                    parentLi.classList.remove('open');
                    var trigger = parentLi.querySelector('a.has-arrow');
                    if (trigger) { trigger.setAttribute('aria-expanded', 'false'); }
                }
            }
        }
    }

    // Restore last opened menu if nothing active
    try {
      var last = sessionStorage.getItem('mdlink_last_menu');
      if (!activeSubmenu && last) {
        for (var t = 0; t < toggles.length; t++) {
          var label = toggles[t].textContent.trim();
          if (label === last) { toggles[t].click(); break; }
        }
      }
    } catch(_) {}
})();
</script>
