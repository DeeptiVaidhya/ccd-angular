import { PatientReportComponent } from './components/patient-report/patient-report.component';
import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { ResourcesComponent } from './components/resources/resources.component';
import { PatientEducationComponent } from './components/patient-education/patient-education.component';
import { ReferencesComponent } from './components/references/references.component';
import { AddPatientComponent } from './components/add-patient/add-patient.component';
import { AddPrescriptionComponent } from './components/add-prescription/add-prescription.component';
import { PatientDetailsComponent } from './components/patient-details/patient-details.component';
import { BillingCodeComponent } from './components/billing-code/billing-code.component';
import { AssistanceComponent } from './components/assistance/assistance.component';
const routes: Routes = [
	{ path: '', component: DashboardComponent, pathMatch: "full"},
	{ path: "dashboard", component: DashboardComponent},
	{ path: "resource", component: ResourcesComponent },
	{ path: "patient-education", component: PatientEducationComponent},
	{ path: "references", component: ReferencesComponent},
	{ path: "add-patient", component: AddPatientComponent},
	{ path: "add-prescription/:patientId", component: AddPrescriptionComponent},
	{ path: "patient-details/:patientId", component: PatientDetailsComponent},
	{ path: "add-billing-code/:patientId", component: BillingCodeComponent},
	{ path: "update-patient/:patientId", component: AddPatientComponent},
	{ path: "patient-report/:patientId", component: PatientReportComponent},
	{ path: "assistance/:patientId", component: AssistanceComponent},
];
@NgModule({
  imports: [RouterModule.forRoot(routes, {useHash: true})],
  exports: [RouterModule]
})
export class AppRoutingModule { }
