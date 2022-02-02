import { TokenInterceptor } from './interceptor/token.interceptor';
import { PatientService } from './service/patient.service';
import { HelperService } from './service/helper.service';
import { AuthService } from './service/auth.service';
import { PatientGuard } from './guards/patient.guard';
import { BrowserModule } from '@angular/platform-browser';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA  } from '@angular/core';
import { NgxSpinnerModule } from "ngx-spinner";
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { HeaderComponent } from './components/sub-components/header/header.component';
import { FooterComponent } from './components/sub-components/footer/footer.component';
import { ResourcesComponent } from './components/resources/resources.component';
import { PatientEducationComponent } from './components/patient-education/patient-education.component';
import { ReferencesComponent } from './components/references/references.component';
import { SubHeaderComponent } from './components/sub-components/sub-header/sub-header.component';
import { PatientRecordComponent } from './components/patient-record/patient-record.component';
import { NotesComponent } from './components/notes/notes.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AddPatientComponent } from './components/add-patient/add-patient.component';
import { AddPrescriptionComponent } from './components/add-prescription/add-prescription.component';
import { ReactiveFormsModule } from '@angular/forms';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { ToastrModule } from 'ngx-toastr';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { PatientDetailsComponent } from './components/patient-details/patient-details.component';
import { BillingCodeComponent } from './components/billing-code/billing-code.component';
import { PatientReportComponent } from './components/patient-report/patient-report.component';
import { CurrencyPipe, PercentPipe } from '@angular/common';
import { AssistanceComponent } from './components/assistance/assistance.component';
import { PopoverModule } from 'ngx-bootstrap/popover';
import { ModalModule } from 'ngx-bootstrap/modal';
@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    HeaderComponent,
    FooterComponent,
    ResourcesComponent,
    PatientEducationComponent,
    ReferencesComponent,
    SubHeaderComponent,
    PatientRecordComponent,
    NotesComponent,
    AddPatientComponent,
    AddPrescriptionComponent,
    PatientDetailsComponent,
    BillingCodeComponent,
    PatientReportComponent,
    AssistanceComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    AppRoutingModule,
    NgxSpinnerModule,
    HttpClientModule,
    ReactiveFormsModule,
    BsDatepickerModule.forRoot(),
    ToastrModule.forRoot(),
    PopoverModule.forRoot(),
    ModalModule.forRoot(),
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  providers: [
    HttpClientModule,
		PatientGuard,
		AuthService,
		HelperService,
    PatientService,
    CurrencyPipe,
    PercentPipe,
		{
			provide: HTTP_INTERCEPTORS,
			useClass: TokenInterceptor,
			multi: true
		}
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
