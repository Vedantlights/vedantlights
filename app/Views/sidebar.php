<?= $this->include('header'); ?>
        <!-- ===== Left-Sidebar ===== -->
        <aside class="sidebar">
            <div class="scroll-sidebar">
               
                <nav class="sidebar-nav side_bar">
                    <ul id="side-menu">
                        <li>
                            <a class="waves-effect" href="<?php echo base_url();?>admindashboard" aria-expanded="false"><i class="fa fa-dashboard fa-fw"></i> <span class="hide-menu"> Dashboard </span></a>
                          </li>
						<li>
                            <a class="waves-effect" aria-expanded="false"><i class="fa fa-server fa-fw"></i> <span class="hide-menu"> Master Details </span></a>
                            <ul aria-expanded="false" class="collapse">
                                
                                <li> <a href="<?php echo base_url();?>StateController"><i class="fa fa-dot-circle-o" aria-hidden="true"> </i>State Details</a> </li>
                                <li> <a href="<?php echo base_url();?>DistrictController"> <i class="fa fa-dot-circle-o" aria-hidden="true"> </i>District Details</a> </li>
                              <li>
                                <li> <a href="<?php echo base_url();?>TalukaController"> <i class="fa fa-dot-circle-o" aria-hidden="true"> </i>Taluka Details</a> </li>
                              
                            </ul>
                        </li>
						
                      <li>
                            <a class="waves-effect" aria-expanded="false"><i class="fa fa-server fa-fw"></i> <span class="hide-menu"> Employee Details </span></a>
                            <ul aria-expanded="false" class="collapse">
                                
                                <li> <a href="<?php echo base_url();?>EmployeeController"><i class="fa fa-dot-circle-o" aria-hidden="true"> </i>Add Employee</a> </li>
                                <li> <a href="<?php echo base_url();?>EmployeeController/employeeDetails"> <i class="fa fa-dot-circle-o" aria-hidden="true"> </i>Employee Details</a> </li>
                                 <li> <a href="<?php echo base_url();?>EmployeeController/dailyemployeeDetails"> <i class="fa fa-dot-circle-o" aria-hidden="true"> </i>Daily Employee</a> </li>
                             
                            </ul>
                        </li>
                        
					   <li>
                            <a class="waves-effect" href="<?php echo base_url();?>VendorController" aria-expanded="false"><i class="fa fa-server fa-fw"></i> <span class="hide-menu">Vendor Details</span></a>
                           
                        </li>
                        <li>
                            <a class="waves-effect" href="<?php echo base_url();?>VisitController" aria-expanded="false"><i class="fa fa-server fa-fw"></i> <span class="hide-menu">Visit Details</span></a>
                           
                        </li>
                         <li>
                            <a class="waves-effect" href="<?php echo base_url();?>VisitController/employeeleaveDetails" aria-expanded="false"><i class="fa fa-server fa-fw"></i> <span class="hide-menu">Leave Details</span></a>
                           
                        </li>

						<li>
                            <a class="waves-effect" href="<?php echo base_url();?>log_out" aria-expanded="false"><i class="fa fa-sign-out fa-fw"></i> <span class="hide-menu">Log Out</span></a>
                           
                        </li>
					
                     
                    </ul>
                </nav>
                
            </div>
        </aside>
        <!-- ===== Left-Sidebar-End ===== -->