import { TreatmentService } from './../../service/treatment.service';
import { HelperService } from './../../service/helper.service';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { RouterModule, Router, ActivatedRoute } from '@angular/router';
import { PatientService } from './../../service/patient.service';
import { CurrencyPipe } from '@angular/common';
@Component({
  selector: 'app-billing-code',
  templateUrl: './billing-code.component.html',
  styleUrls: ['./billing-code.component.scss']
})
export class BillingCodeComponent implements OnInit {
	patientBillingCodeForm: FormGroup;
	patientId:any;
	showProviderFieldsFlag:boolean = false;
	showFieldsFlag:boolean = false;
	patientDetails:any = [];
	ccsProvider:any;
	freqReferral:any = '0';
	coverd:any;
	providerList:any =[];
	cptCodeList:any =[];
	modeAllowed:any;
	billingCodeList:any=[];
	addFlag:boolean = false;
	billingCodeDetails:any = [];
	updateFlag:boolean = false;
  	constructor(
		private formBuilder: FormBuilder,
		public route: Router,
		private treatmentService: TreatmentService,
		private toastr: ToastrService,
		private helper: HelperService,
		private activeRoute: ActivatedRoute,
		private patientService: PatientService,
		private currencyPipe : CurrencyPipe
  ) { }

  	ngOnInit(): void {
		 this.activeRoute.params.subscribe(params => {
			this.patientId = params['patientId'];
		});
		this.patientBillingCodeForm = this.formBuilder.group(
			{
				co_pay:[
					'',
					Validators.compose([
						Validators.pattern(/^\d+(\.\d{1,2})?$/),
						Validators.required,
					])
				],
				ccs_provider:[
						'',
						Validators.required,
				],
				frequent_referral:[
						'0',
						Validators.required,
				],
				provider:[
						'',
						Validators.required,
				],
				cpt_code:[
						'',
						Validators.required,
				],
				no_of_units:[
					'',
					Validators.compose([
						Validators.pattern(/^[0-9]+$/),
						Validators.required,
					])
				],
				covered:[
					'',
					Validators.required,
				],
				patient_id:[
					this.patientId
				],
				mode_allowed:[
					''
				],
				id:[
					''
				]
			}
		);
		this.getpatientDetails();
		this.getBillingCodeList();
		this.patientBillingCodeForm.controls.cpt_code.setValue("");
		this.patientBillingCodeForm.controls.provider.setValue("");
	}
	  
	addBillingCode(){
		this.patientBillingCodeForm.controls.ccs_provider.setValue(this.ccsProvider);
		this.patientBillingCodeForm.controls.covered.setValue(this.coverd);
		this.patientBillingCodeForm.controls.mode_allowed.setValue(this.modeAllowed);
		
		if(this.ccsProvider == '0')
			this.patientBillingCodeForm.controls.frequent_referral.setValue(this.freqReferral);
	
		if(this.patientBillingCodeForm.valid){
			this.treatmentService.addBillingCode(this.patientBillingCodeForm.value).subscribe(res =>{
				if(res.status == 'success'){
					this.toastr.success(res.msg);
					this.billingCodeList = res.data;
					this.addFlag = false;
					this.updateFlag = false;
					this.showFieldsFlag = false;
					this.showProviderFieldsFlag = false;
					this.ccsProvider = '';
					this.coverd = '';
					this.freqReferral = '';
					this.patientBillingCodeForm.reset();
					this.patientBillingCodeForm.controls.patient_id.setValue(this.patientId);
				}else{
					this.toastr.error(res.msg)
				}
			});
		}else{
			this.helper.validateAllFormFields(this.patientBillingCodeForm);
		}
	}

	getProviderList(){
		let data = {
			'ccs_provider': this.ccsProvider,
			'ins_type':this.patientDetails.insurance_type,
			'frequent_referral': this.freqReferral
		};

		this.treatmentService.getProvider(data).subscribe(res =>{
			if(res.status == 'success'){
				this.providerList = res.data;
				
			}else{
				this.toastr.error(res.msg);
			}
		});
	}

	getCPTCode(){
		let data ={
			'provider':this.patientBillingCodeForm.value.provider,
			'ins_type' : this.patientDetails.insurance_type
		}
		this.treatmentService.getCptCode(data).subscribe(res =>{
			if(res.status =='success'){
				this.cptCodeList = res.data;
				this.showFieldsFlag = true;
				console.log(this.billingCodeDetails.length>0);
				if(this.updateFlag){
					for (let index = 0; index < this.cptCodeList.length; index++) {
						if(this.cptCodeList[index]['cpt_proc_desc_concat'] == this.billingCodeDetails['cpt_code']){
							let mode = this.cptCodeList[index]['mode_allowed'].split('$');
							this.modeAllowed = mode[1];
							console.log(this.modeAllowed);
							this.patientBillingCodeForm.controls.mode_allowed.setValue(this.modeAllowed);
							this.patientBillingCodeForm.controls.cpt_code.setValue(this.cptCodeList[index]['cpt_proc_desc_concat']);
						}
					}
				}
			}else{
				this.toastr.error(res.msg);
			}
		});
	}

	getpatientDetails(){
		this.patientService.getPatient({'patientId' : this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				this.patientDetails = res.data;
			}else{
				this.toastr.error(res.msg);
			}
		})
	}

	ccsProviderChange(value:any){
		this.ccsProvider = value;
		this.showProviderFieldsFlag = true;
		this.patientBillingCodeForm.controls.ccs_provider.setValue(this.ccsProvider);
		if(value == '1'){
			this.freqReferral = '0';
			this.patientBillingCodeForm.controls.frequent_referral.setValue('0');
		}
		this.getProviderList();
	}

	coverdChange(value:any){
		this.coverd = value;
		this.patientBillingCodeForm.controls.covered.setValue(this.coverd);
	}

	changeCptCode(event:Event){
		let code = (<HTMLInputElement>event.target).value;
		for(let i = 0; i<this.cptCodeList.length; i++){
			if(this.cptCodeList[i]['cpt_proc_desc_concat'] == code){
				let mode = this.cptCodeList[i]['mode_allowed'].split('$');
				this.modeAllowed = mode[1];
			}
		}
		console.log(this.modeAllowed);
	}

	referralChange(value:any){
		this.freqReferral = value;
		this.getProviderList();
		this.patientBillingCodeForm.controls.frequent_referral.setValue(this.freqReferral);
	}

	addBillingCodeFlag(){
		this.addFlag = true;
	}

	getBillingCodeList(){
		this.treatmentService.getBillingCodeList({'patientId' : this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				this.billingCodeList = res.data;
			}
		});
	}

	getbillingCodeDetails(id:any){
		this.addFlag = true;
		this.updateFlag = true;
		this.treatmentService.getBillingCodeDetails({'id':id}).subscribe(res =>{
			if(res.status == 'success'){
				this.billingCodeDetails = res.data;
				Object.keys(this.patientBillingCodeForm.controls).forEach(field => {
					this.ccsProvider = this.billingCodeDetails['ccs_provider'];
					this.coverd = this.billingCodeDetails['covered'];
					this.freqReferral = this.billingCodeDetails['frequent_referral'];
					this.showFieldsFlag = true;
					this.showProviderFieldsFlag = true;
					this.patientBillingCodeForm.controls[field].setValue(this.billingCodeDetails[field]);
				});
				this.getProviderList();
				setTimeout(() => {
					this.getCPTCode();
				}, 1000);
			}
		});
	}

	navigateTopatientRecord(){
		this.route.navigate(['/patient-details/'+this.patientId]);
	}

	transformAmount(element){
		let formattedAmount;
		let value = element.target.value.split("$");
		
		if(value.length == 1){
			formattedAmount = this.currencyPipe.transform(element.target.value, '$');
		}else{
			value[1] = value[1].replace(',', '');
			formattedAmount = this.currencyPipe.transform(value[1], '$');
		}
	
		element.target.value = formattedAmount;
	}

	back(){
		this.patientBillingCodeForm.reset();
		this.patientBillingCodeForm.controls.patient_id.setValue(this.patientId);
		this.addFlag = false;
		this.updateFlag = false;
		this.showFieldsFlag = false;
		this.showProviderFieldsFlag = false;
		this.ccsProvider = '';
		this.coverd = '';
		this.freqReferral = '';
	}

	saveBillingCodeData(){
		this.treatmentService.updatePatientPaymentData({'patient_id': this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				this.toastr.success(res.msg);
			}else{
				this.toastr.error(res.msg);
			}
		});
	}

}
