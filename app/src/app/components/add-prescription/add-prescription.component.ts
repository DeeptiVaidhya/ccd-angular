import { TreatmentService } from './../../service/treatment.service';
import { HelperService } from './../../service/helper.service';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { RouterModule, Router, ActivatedRoute } from '@angular/router';
import { CurrencyPipe } from '@angular/common';
@Component({
	selector: 'app-add-prescription',
	templateUrl: './add-prescription.component.html',
	styleUrls: ['./add-prescription.component.scss']
})
export class AddPrescriptionComponent implements OnInit {
	patientPrescriptionForm: FormGroup;
	patientId:any;
	prescriptionList:any =[];
	addPrescriptionFlag:boolean =false;
	prescriprionData:any =[];
	searchList:any = [];
	isCovered:boolean = false;
	constructor(
		private formBuilder: FormBuilder,
		private treatmentService: TreatmentService,
		private toastr: ToastrService,
		private helper: HelperService,
		private activeRoute: ActivatedRoute,
		private route: Router,
		private currencyPipe : CurrencyPipe
		
	) { }

	ngOnInit(): void {
		this.activeRoute.params.subscribe(params => {
            this.patientId = params['patientId'];
		});
		this.patientPrescriptionForm = this.formBuilder.group(
			{
				co_pay:[
					'',
					Validators.compose([
						Validators.pattern(/^\d+(\.\d{1,2})?$/),
						Validators.required,
					])
				],
				covered:[
					'',
					Validators.required,
				],
				drug_name:[
					'',
					Validators.compose([
						Validators.pattern(/^.*[^ ].*/),
						Validators.required,
					])
				],
				unit_price:[
					'',
					Validators.compose([
						Validators.pattern(/^\d+(\.\d{1,2})?$/),
						Validators.required,
					])
				],
				quantity:[
					'',
					Validators.compose([
						Validators.pattern(/^[0-9]+$/),
						Validators.required,
					])
				],
				patient_id:[
					this.patientId,
				],
				id:[
					'',
				]
			}
		);
		this.getPrescriptionList();
		
	}

	addPrescription(){
		if(this.patientPrescriptionForm.valid){
			this.treatmentService.addPatientPrescriptions(this.patientPrescriptionForm.value).subscribe(res =>{
				if(res.status == 'success'){
					this.toastr.success(res.msg);
					this.prescriptionList = res.data;
					this.addPrescriptionFlag = false;
					this.patientPrescriptionForm.reset();
					this.patientPrescriptionForm.controls.patient_id.setValue(this.patientId);
				}else{
					this.toastr.error(res.msg);
				}
			});
		}else{
			this.helper.validateAllFormFields(this.patientPrescriptionForm);
		}
	}

	getPrescriptionList(){
		this.treatmentService.getPatientPrescriptions({'patient_id' : this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				this.prescriptionList = res.data;
			}else{
				this.toastr.error(res.msg);
			}
		});
	}

	back(){
		this.patientPrescriptionForm.reset();
		this.patientPrescriptionForm.controls.patient_id.setValue(this.patientId);
		this.addPrescriptionFlag = false;
	}

	addPrescriptionShow(){
		this.addPrescriptionFlag = true;
	}

	getPrescriptionDetails(id:any){
		this.addPrescriptionFlag = true;
		this.treatmentService.getPatientPrescriptionsDetails({'prescriptionId':id}).subscribe(res =>{
			if(res.status =='success'){
				this.prescriprionData = res.data;
				Object.keys(this.patientPrescriptionForm.controls).forEach(field => {
					this.patientPrescriptionForm.controls[field].setValue(this.prescriprionData[field], { onlySelf: true });
				});
			}else{
				this.addPrescriptionFlag = false;
				this.toastr.error(res.msg);
			}
		});
	}

	navigateTopatientRecord(){
		this.route.navigate(['/patient-details/'+this.patientId]);
	}

	searchPrescriptions(event){
		let string = new String(event.target.value);
		if (string.trim().length > 2) {
			this.treatmentService.searchPatientPrescription({'key':event.target.value}).subscribe(res =>{
				if(res.status == 'success'){
					this.searchList = res.data;
				}
			});
		}
	}


	selectPrescription(id:any, name:any, unit_price:any){
		this.patientPrescriptionForm.controls.drug_name.setValue(name);
		this.patientPrescriptionForm.controls.unit_price.setValue(unit_price);
		this.searchList = [];
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

	coverdChange(value:any){
		if(value == '1'){
			this.isCovered = true;
			this.patientPrescriptionForm.controls.covered.setValue('1');
		}else{
			this.isCovered = false;
			this.patientPrescriptionForm.controls.co_pay.setValue(0);
			this.patientPrescriptionForm.controls.covered.setValue('0');
		}
	}
}
