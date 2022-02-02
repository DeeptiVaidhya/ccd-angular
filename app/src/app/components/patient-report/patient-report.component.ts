import { CurrencyPipe } from '@angular/common';
import { HelperService } from './../../service/helper.service';
import { PatientService } from './../../service/patient.service';
import { Component, OnInit, ElementRef, ViewChild, TemplateRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import html2canvas from "html2canvas";
import { ToastrService } from 'ngx-toastr';
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import { TreatmentService } from 'src/app/service/treatment.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';

am4core.useTheme(am4themes_animated);
@Component({
	selector: 'app-patient-report',
	templateUrl: './patient-report.component.html',
	styleUrls: ['./patient-report.component.scss']
})
export class PatientReportComponent implements OnInit {
	modalRef: BsModalRef;
	patientDetails:any = [];
	paymentDetails:any = [];
	estPatientCost:any = [];
	coveredData:any = [];
	patientId:any;
	estimeateData:any = ['1','2','3','4','5','6','7'];
	labelValue:any =[];
	treatmentPalnDetails:any =[];
	prescriptionPay:any;
	assistanceData:any =[];
	date = new Date();
	modalTitle:any ='';
	modalType:boolean;
	constructor(
		public route: Router,
		private formBuilder: FormBuilder,
		private patientService: PatientService,
		private toastr: ToastrService,
		private helper: HelperService,
		private activeRoute: ActivatedRoute,
		private currencyPipe : CurrencyPipe,
		private treatmentService: TreatmentService,
		private modalService: BsModalService
	) { }

	ngOnInit(): void {
		this.activeRoute.params.subscribe(params => {
			this.patientId = params['patientId'];
		});
		this.getPatientDetails();
		
	}

	configureChart() {
		
	}

	getPatientDetails(){
		this.patientService.getPatient({'patientId':this.patientId}).subscribe(res =>{
			if(res.status == 'success'){
				let copayCoveredPrescriptions,
					coveredPrescriptions,
					nonCoveredPrescriptions,
					insurancePays;
				copayCoveredPrescriptions = res.prescriptions['copay_covered_prescriptions'];
				nonCoveredPrescriptions = res.prescriptions['non_covered_prescriptions'];
				coveredPrescriptions = res.prescriptions['covered_prescriptions'];
				insurancePays = res.prescriptions['insurance_pays'];
				this.patientDetails = res.data;
				this.paymentDetails = res.payment_data;
				this.treatmentPalnDetails = res.treatment_plan;
				this.assistanceData = res.all_assistance;
				this.prescriptionPay  = res.total_prescription_price;
				if(!this.paymentDetails){
					this.paymentDetails = {
						'co_pays' : 0,
						'individual_deductible' : 0,
						'covered_services' : 0,
						'non_covered_services' : 0,
						'estimated_patient_payment' : 0,
						'co_insurance':0
					};
				}
				this.labelValue.push(
					{'title':'Estimated Patient’s OOP Cost','center-value':this.transformAmount(parseFloat(this.paymentDetails['co_pays'])+parseFloat(this.paymentDetails['individual_deductible'])+parseFloat(this.paymentDetails['co_insurance'])+(parseFloat(copayCoveredPrescriptions)+parseFloat(nonCoveredPrescriptions)))},
					{'title':'Total Cost','center-value':this.transformAmount(parseFloat(this.paymentDetails['covered_services'])+parseFloat(coveredPrescriptions)+parseFloat(this.paymentDetails['non_covered_services'])+parseFloat(nonCoveredPrescriptions))},
					{'title':'Covered Cost','center-value':this.transformAmount(parseFloat(this.paymentDetails['estimated_patient_payment'])+parseFloat(coveredPrescriptions)+parseFloat(nonCoveredPrescriptions)+parseFloat(this.paymentDetails['covered_services'])+parseFloat(this.paymentDetails['non_covered_services'])-(parseFloat(this.paymentDetails['estimated_patient_payment'])+parseFloat(coveredPrescriptions)+parseFloat(nonCoveredPrescriptions)))},
					{'title':'Individual Deductible','center-value':this.transformAmount(parseFloat(this.patientDetails['individual_deductible']))},
					{'title':'Family Deductible','center-value':this.transformAmount(parseFloat(this.patientDetails['family_deductible']))},
					{'title':'Individual Max OOP','center-value':this.transformAmount(parseFloat(this.patientDetails['individual_max_out_of_pocket']))},
					{'title':'Family Max OOP','center-value':this.transformAmount(parseFloat(this.patientDetails['family_max_out_of_pocket']))}
				);
				this.estPatientCost = {
					'1':[
						{ "sector" : "Co-Pay", "size": this.transformAmount(parseFloat(this.paymentDetails['co_pays'])), 'color':'#8E54E9'},
						{ "sector" : "Deductible", "size": this.transformAmount(parseFloat(this.paymentDetails['individual_deductible'])>0 ? parseFloat(this.paymentDetails['individual_deductible']) : 0), 'color':'#134E5E'},
						{ "sector" : "Co-insurance", "size": this.transformAmount(parseFloat(this.paymentDetails['co_insurance'])), 'color':'#F09819'},
						{ "sector" : "Prescriptions", "size": this.transformAmount(parseFloat(coveredPrescriptions)+parseFloat(nonCoveredPrescriptions)), 'color':'#1A2980'},
					],
					'2':[
						{ "sector" : "Covered Services", "size": this.transformAmount(parseFloat(this.paymentDetails['covered_services'])+parseFloat(coveredPrescriptions)), 'color':'#004B9C'},
						{ "sector" : "Uncovered Services", "size": this.transformAmount(parseFloat(this.paymentDetails['non_covered_services'])+parseFloat(nonCoveredPrescriptions)), 'color':'#C3D4E0'},
					],
					'3':[
						{'sector' : "Patient Portion", "size": this.transformAmount(parseFloat(this.paymentDetails['covered_services'])+parseFloat(this.paymentDetails['non_covered_services'])-(parseFloat(this.paymentDetails['estimated_patient_payment'])+parseFloat(coveredPrescriptions)+parseFloat(nonCoveredPrescriptions))), 'color':'#134E5E'},
						{'sector' : "Insurance Portion", "size": this.transformAmount(parseFloat(this.paymentDetails['estimated_patient_payment'])+parseFloat(copayCoveredPrescriptions)+parseFloat(nonCoveredPrescriptions)), 'color':'#C3D4E0'}
					],
					'4':[
						{"sector" : "Individual Deductible Paid", "size": this.transformAmount(parseFloat(this.patientDetails['individual_deductible_paid'])), 'color':'#EB3349'},
						{"sector" : "Unmet Individual Deductible", "size": this.transformAmount((parseFloat(this.patientDetails['individual_deductible'])-parseFloat(this.patientDetails['individual_deductible_paid']))), 'color':'#C3D4E0'}
					],
					'5':[
						{"sector" : "Family Deductible Paid", "size": this.transformAmount(parseFloat(this.patientDetails['family_deductible_paid'])), 'color':'#DE6262'},
						{"sector" : "Unmet Family Deductible", "size": this.transformAmount((parseFloat(this.patientDetails['family_deductible'])-parseFloat(this.patientDetails['family_deductible_paid']))), 'color':'#C3D4E0'}
					],
					'6':[
						{"sector" : "Individual Max OOP Paid", "size": this.transformAmount(parseFloat(this.patientDetails['individual_max_out_of_pocket_paid'])), 'color':'#F857A6'},
						{"sector" : "Unmet Individual Max OOP", "size": this.transformAmount((parseFloat(this.patientDetails['individual_max_out_of_pocket'])-parseFloat(this.patientDetails['individual_max_out_of_pocket_paid']))), 'color':'#C3D4E0'}
					],
					'7':[
						{"sector" : "Family Max OOP Paid", "size": parseFloat(this.patientDetails['family_max_out_of_pocket_paid']), 'color':'#E43A15'},
						{"sector" : "Unmet Family Max OOP", "size": (parseFloat(this.patientDetails['family_max_out_of_pocket'])-parseFloat(this.patientDetails['family_max_out_of_pocket_paid'])), 'color':'#C3D4E0'}
					]
				}


				this.coveredData.push(
					
				);
				for (let index = 0; index < this.estimeateData.length; index++) {
					
					let chart = am4core.create("chartdiv"+(index+1), am4charts.PieChart);
					chart.innerRadius = 50;
					chart.radius = am4core.percent(70);
					chart.logo.height = -15000;
					chart.responsive.enabled = true;
					let label = chart.seriesContainer.createChild(am4core.Label);
					label.text = this.labelValue[index]['center-value'];
					label.horizontalCenter = "middle";
					label.verticalCenter = "middle";
					
					label.fontSize = 20;
					label.fontWeight = 'bold';
					label.fill = am4core.color('#006EE3');

					let label1 = chart.chartContainer.createChild(am4core.Label);
					label1.text = this.labelValue[index]['title'];
					label1.align = "center";
					label1.horizontalCenter = "middle";
					label.verticalCenter = "middle";
					
					if(index>0){
						label1.fontSize = 18;
						label1.y = 330;
					}else{
						label1.fontSize = 24;
						label1.y = 350;
					}
					label1.fontWeight ='bold';
					label1.fill = am4core.color('#373A3C');
					
					// Add and configure Series
					let pieSeries = chart.series.push(new am4charts.PieSeries());
					pieSeries.dataFields.value = "size";
					pieSeries.dataFields.category = "sector";
					pieSeries.slices.template.propertyFields.fill = "color";
					if(index>0){
						pieSeries.labels.template.fontSize = 12;
						pieSeries.alignLabels = false;
					}else{
						pieSeries.labels.template.fontSize = 14;
					}
					pieSeries.labels.template.fill = am4core.color('#1F344D');
					pieSeries.labels.template.maxWidth = 80;
					pieSeries.labels.template.wrap = true;
					pieSeries.labels.template.fontWeight = 'bold';
					// pieSeries.ticks.template.disabled = true;
					pieSeries.labels.template.text = "{category} \n ${value.formatNumber('#,###')}";
					chart.data = this.estPatientCost[this.estimeateData[index]];
				}
				
			}
		});
	}

	downloadPDF(): void {
		html2canvas(document.getElementById("report")).then(canvas => {
			let newCanvas = document.createElement("canvas");//canvas.getContext('2d');
			newCanvas.width = canvas.width;
			newCanvas.height = canvas.height;
			let destCtx = newCanvas.getContext('2d');
			destCtx.drawImage(canvas,0,0);
			destCtx.drawImage(canvas,0,canvas.height);
			
				let w = window.open(
					destCtx.canvas.toDataURL("image/png"),
					this.patientDetails['patient_name']+' '+"Report",
					"_blank"
				);
				w.document.body.appendChild(canvas);
				w.print();
				w.close();
		});
	}
	
	navigateTopatientRecord(){
		this.route.navigate(['/patient-details/'+this.patientId]);
	}

	transformAmount(value = 0){
		let curValue;
		curValue = this.currencyPipe.transform(value).split('.');
		console.log(curValue[0]);
		return curValue[0];
	}

	addAssistanceToPatient(patientProgramId:any, id:any, type:any, index:any){
		const proId = [];
		proId.push({'id':id, 'assistance_type': type, 'patient_id':this.patientId, 'patient_program_id':patientProgramId});
		this.treatmentService.addprogramToPatient(proId).subscribe(res=>{
			if(res.status == 'success'){
				this.toastr.success(res.msg);
				this.getPatientDetails();
			}
		})
	}

	openModal(template: TemplateRef<any>, type:any) {
		this.modalTitle = type;
		this.modalRef = this.modalService.show(template);
	  }
}
