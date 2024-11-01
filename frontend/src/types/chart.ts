export interface ChartDataTypes {
  labels: string[]
  datasets: ChartDataSetTypes[]
}

interface ChartDataSetTypes {
  label: string
  type?: 'bar' | 'line'
  data: number[]
  fill?: boolean
  borderColor?: string
  tension?: number
  backgroundColor?: string
}

export interface ChartOptionsTypes {
  maintainAspectRatio: boolean
  aspectRatio: number
  plugins: {
    legend: {
      labels: {
        color: string
      }
    }
    tooltips?: {
      mode: string
      intersect: boolean
    }
  }
  scales: {
    x: ScalesTypes
    y: ScalesTypes
  }
}

interface ScalesTypes {
  stacked?: boolean
  ticks: {
    color: string
  }
  grid: {
    color: string
  }
}
